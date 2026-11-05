<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Production;
use App\Models\Stock;
use App\Models\StockLog;
use App\Models\DeliveryItem;
use App\Models\InventoryCostLayer;
use App\Models\InventoryCostAllocation;
use App\Models\InventoryCostAdjustment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use InvalidArgumentException;
use RuntimeException;

class FifoInventoryService
{
    /**
     * Create a new FIFO cost layer when production is completed
     */
    public function createCostLayer(
        Product $product,
        float $quantity,
        float $unitCost,
        string $batchNumber,
        ?string $receivedDate = null,
        ?Production $production = null,
        ?string $notes = null
    ): InventoryCostLayer {
        if ($quantity <= 0) {
            throw new InvalidArgumentException("Production quantity for FIFO layer must be greater than zero.");
        }

        $receivedDate = $receivedDate ?: date('Y-m-d');
        $totalCost = round($quantity * $unitCost, 2);

        return InventoryCostLayer::create([
            'product_id' => $product->id,
            'production_id' => $production?->id,
            'batch_number' => $batchNumber,
            'received_date' => $receivedDate,
            'initial_qty' => $quantity,
            'remaining_qty' => $quantity,
            'unit_cost' => $unitCost,
            'total_cost' => $totalCost,
            'status' => 'active',
            'notes' => $notes,
        ]);
    }

    /**
     * Consume stock using FIFO algorithm across oldest active layers
     *
     * @param Product $product
     * @param float $requiredQty
     * @param DeliveryItem $deliveryItem
     * @return array ['total_cogs' => float, 'allocations' => array]
     */
    public function allocateFifo(Product $product, float $requiredQty, DeliveryItem $deliveryItem): array
    {
        if ($requiredQty <= 0) {
            throw new InvalidArgumentException("Allocation quantity must be greater than zero.");
        }

        // Lock product's stock row to guarantee no race condition
        $stock = Stock::where('product_id', $product->id)->lockForUpdate()->first();
        $availableTotal = $stock ? (float)$stock->current_stock : 0;

        // Retrieve active layers ordered by oldest received date and ID
        $layers = InventoryCostLayer::where('product_id', $product->id)
            ->where('status', 'active')
            ->where('remaining_qty', '>', 0.0001)
            ->orderBy('received_date', 'asc')
            ->orderBy('id', 'asc')
            ->lockForUpdate()
            ->get();

        $totalAvailableInLayers = (float) $layers->sum('remaining_qty');

        if ($totalAvailableInLayers < $requiredQty - 0.0001) {
            throw new RuntimeException(
                sprintf(
                    "Insufficient FIFO Inventory for '%s'! Requested: %.2f %s, Available in cost layers: %.2f %s.",
                    $product->name,
                    $requiredQty,
                    $product->unit?->short_code ?? 'Units',
                    $totalAvailableInLayers,
                    $product->unit?->short_code ?? 'Units'
                )
            );
        }

        $remainingToAllocate = $requiredQty;
        $totalCogs = 0.0;
        $allocations = [];

        foreach ($layers as $layer) {
            if ($remainingToAllocate <= 0.0001) {
                break;
            }

            $currentLayerAvailable = (float) $layer->remaining_qty;
            $allocateQty = min($remainingToAllocate, $currentLayerAvailable);
            $allocCost = round($allocateQty * (float) $layer->unit_cost, 2);

            // Record allocation line
            $allocation = InventoryCostAllocation::create([
                'delivery_item_id' => $deliveryItem->id,
                'inventory_cost_layer_id' => $layer->id,
                'product_id' => $product->id,
                'allocated_qty' => $allocateQty,
                'unit_cost' => $layer->unit_cost,
                'total_cost' => $allocCost,
                'allocated_at' => now(),
            ]);

            // Deduct from layer
            $newRemaining = round($currentLayerAvailable - $allocateQty, 4);
            $layer->remaining_qty = $newRemaining;
            if ($newRemaining <= 0.0001) {
                $layer->status = 'exhausted';
                $layer->remaining_qty = 0;
            }
            $layer->save();

            $totalCogs = round($totalCogs + $allocCost, 2);
            $allocations[] = $allocation;
            $remainingToAllocate = round($remainingToAllocate - $allocateQty, 4);
        }

        // Update delivery item snapshot fields
        $fifoUnitCost = $requiredQty > 0 ? round($totalCogs / $requiredQty, 2) : 0;
        $grossProfit = round((float)$deliveryItem->total_price - $totalCogs, 2);

        $deliveryItem->fifo_unit_cost = $fifoUnitCost;
        $deliveryItem->fifo_total_cost = $totalCogs;
        $deliveryItem->gross_profit = $grossProfit;
        $deliveryItem->save();

        return [
            'total_cogs' => $totalCogs,
            'allocations' => $allocations,
            'gross_profit' => $grossProfit,
        ];
    }

    /**
     * Safely restore FIFO layers when a delivery item is cancelled or reversed
     */
    public function rollbackAllocations(DeliveryItem $deliveryItem): void
    {
        $allocations = InventoryCostAllocation::with('costLayer')
            ->where('delivery_item_id', $deliveryItem->id)
            ->get();

        foreach ($allocations as $alloc) {
            $layer = $alloc->costLayer;
            if ($layer) {
                // Lock layer
                $lockedLayer = InventoryCostLayer::where('id', $layer->id)->lockForUpdate()->first();
                if ($lockedLayer) {
                    $lockedLayer->remaining_qty = round((float)$lockedLayer->remaining_qty + (float)$alloc->allocated_qty, 4);
                    if ($lockedLayer->status === 'exhausted') {
                        $lockedLayer->status = 'active';
                    }
                    $lockedLayer->save();
                }
            }
            $alloc->delete();
        }
    }

    /**
     * Check if a production record can be safely edited or if items have been consumed
     */
    public function checkProductionEditSafety(Production $production, float $newQty): void
    {
        $layer = InventoryCostLayer::where('production_id', $production->id)->first();
        if (!$layer) {
            return;
        }

        $consumedQty = round((float)$layer->initial_qty - (float)$layer->remaining_qty, 4);
        if ($consumedQty > 0.0001 && $newQty < $consumedQty) {
            throw new RuntimeException(
                sprintf(
                    "Cannot reduce production quantity to %.2f because %.2f units have already been dispatched in deliveries via FIFO!",
                    $newQty,
                    $consumedQty
                )
            );
        }
    }

    /**
     * Check if a production record can be safely deleted
     */
    public function checkProductionDeleteSafety(Production $production): void
    {
        $layer = InventoryCostLayer::where('production_id', $production->id)->first();
        if (!$layer) {
            return;
        }

        $consumedQty = round((float)$layer->initial_qty - (float)$layer->remaining_qty, 4);
        if ($consumedQty > 0.0001) {
            throw new RuntimeException(
                sprintf(
                    "Cannot delete production batch #%s because %.2f units have already been delivered to customers!",
                    $layer->batch_number,
                    $consumedQty
                )
            );
        }
    }

    /**
     * Adjust a production layer quantity and update stock
     */
    public function updateProductionLayer(Production $production, float $newQty, float $unitCost): void
    {
        $layer = InventoryCostLayer::where('production_id', $production->id)->lockForUpdate()->first();
        if (!$layer) {
            return;
        }

        $this->checkProductionEditSafety($production, $newQty);

        $oldQty = (float) $layer->initial_qty;
        $diff = round($newQty - $oldQty, 4);

        $layer->initial_qty = $newQty;
        $layer->remaining_qty = round((float)$layer->remaining_qty + $diff, 4);
        $layer->unit_cost = $unitCost;
        $layer->total_cost = round($newQty * $unitCost, 2);
        if ($layer->remaining_qty > 0 && $layer->status === 'exhausted') {
            $layer->status = 'active';
        }
        $layer->save();

        // Record adjustment audit
        InventoryCostAdjustment::create([
            'inventory_cost_layer_id' => $layer->id,
            'adjustment_type' => 'production_edit',
            'qty_change' => $diff,
            'cost_impact' => round($diff * $unitCost, 2),
            'reason' => "Production batch {$production->id} updated from {$oldQty} to {$newQty}",
            'adjusted_by' => auth()->id() ?? null,
        ]);
    }

    /**
     * Delete a production layer safely
     */
    public function deleteProductionLayer(Production $production): void
    {
        $this->checkProductionDeleteSafety($production);
        InventoryCostLayer::where('production_id', $production->id)->delete();
    }

    /**
     * Reconcile all products' Stock::current_stock with sum of remaining FIFO layers
     */
    public function reconcileAllProducts(): array
    {
        $products = Product::with('stock')->get();
        $discrepancies = [];

        foreach ($products as $p) {
            $stockQty = $p->stock ? (float)$p->stock->current_stock : 0.0;
            $layerQty = (float) InventoryCostLayer::where('product_id', $p->id)
                ->where('status', 'active')
                ->sum('remaining_qty');

            $diff = round($stockQty - $layerQty, 4);
            if (abs($diff) > 0.0001) {
                $discrepancies[] = [
                    'product_id' => $p->id,
                    'product_name' => $p->name,
                    'stock_qty' => $stockQty,
                    'fifo_layer_qty' => $layerQty,
                    'difference' => $diff,
                ];
            }
        }

        return $discrepancies;
    }

    /**
     * Get comprehensive FIFO Inventory Valuation Report
     */
    public function getInventoryValuationReport(?int $productId = null): array
    {
        $query = Product::with(['unit', 'stock']);
        if ($productId) {
            $query->where('id', $productId);
        }
        $products = $query->orderBy('name')->get();

        $rows = [];
        $grandTotalQty = 0.0;
        $grandTotalValuation = 0.0;

        foreach ($products as $p) {
            $layers = InventoryCostLayer::where('product_id', $p->id)
                ->where('status', 'active')
                ->where('remaining_qty', '>', 0.0001)
                ->orderBy('received_date', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            $layerDetails = [];
            $prodTotalQty = 0.0;
            $prodTotalValuation = 0.0;

            foreach ($layers as $layer) {
                $remQty = (float) $layer->remaining_qty;
                $uCost = (float) $layer->unit_cost;
                $val = round($remQty * $uCost, 2);
                $ageDays = Carbon::parse($layer->received_date)->diffInDays(Carbon::now());

                $layerDetails[] = [
                    'id' => $layer->id,
                    'batch_number' => $layer->batch_number,
                    'received_date' => $layer->received_date,
                    'age_days' => $ageDays,
                    'initial_qty' => (float) $layer->initial_qty,
                    'remaining_qty' => $remQty,
                    'unit_cost' => $uCost,
                    'layer_valuation' => $val,
                    'notes' => $layer->notes,
                ];

                $prodTotalQty = round($prodTotalQty + $remQty, 4);
                $prodTotalValuation = round($prodTotalValuation + $val, 2);
            }

            $stockOnHand = $p->stock ? (float)$p->stock->current_stock : 0.0;
            $reconciliationStatus = abs($stockOnHand - $prodTotalQty) < 0.001 ? 'balanced' : 'mismatch';

            $rows[] = [
                'product_id' => $p->id,
                'product_name' => $p->name,
                'unit_name' => $p->unit?->name ?? 'Unit',
                'stock_on_hand' => $stockOnHand,
                'fifo_total_qty' => $prodTotalQty,
                'fifo_valuation' => $prodTotalValuation,
                'weighted_avg_cost' => $prodTotalQty > 0 ? round($prodTotalValuation / $prodTotalQty, 2) : (float)$p->production_price,
                'layers' => $layerDetails,
                'reconciliation_status' => $reconciliationStatus,
            ];

            $grandTotalQty = round($grandTotalQty + $prodTotalQty, 4);
            $grandTotalValuation = round($grandTotalValuation + $prodTotalValuation, 2);
        }

        return [
            'products' => $rows,
            'grand_total_qty' => $grandTotalQty,
            'grand_total_valuation' => $grandTotalValuation,
        ];
    }
}
