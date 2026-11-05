<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\InventoryCostLayer;
use App\Models\InventoryCostAllocation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProfitabilityReportingService
{
    /**
     * Product-wise Profitability Report based on historical FIFO allocations
     */
    public function getProductProfitability(?string $fromDate = null, ?string $toDate = null, ?int $productId = null): array
    {
        $fromDate = $fromDate ?: Carbon::now()->startOfMonth()->format('Y-m-d');
        $toDate = $toDate ?: Carbon::now()->format('Y-m-d');

        $query = Product::with(['unit', 'stock']);
        if ($productId) {
            $query->where('id', $productId);
        }
        $products = $query->orderBy('name')->get();

        $rows = [];
        $totalDeliveredQty = 0.0;
        $totalSales = 0.0;
        $totalCogs = 0.0;
        $totalGrossProfit = 0.0;

        foreach ($products as $p) {
            // Current FIFO stock layers
            $fifoLayers = InventoryCostLayer::where('product_id', $p->id)
                ->where('status', 'active')
                ->get();
            $currentFifoQty = (float) $fifoLayers->sum('remaining_qty');
            $currentFifoValue = (float) $fifoLayers->sum(fn($l) => (float)$l->remaining_qty * (float)$l->unit_cost);

            // Delivered items in period
            $deliveryItems = DeliveryItem::where('product_id', $p->id)
                ->whereHas('delivery', function ($q) use ($fromDate, $toDate) {
                    $q->whereBetween('delivery_date', [$fromDate, $toDate]);
                })
                ->get();

            $deliveredQty = (float) $deliveryItems->sum('qty');
            $salesAmount = (float) $deliveryItems->sum('total_price');
            $fifoCogs = (float) $deliveryItems->sum('fifo_total_cost');

            // If historical allocations exist, use sum of allocations
            $allocCogs = (float) InventoryCostAllocation::where('product_id', $p->id)
                ->whereBetween('allocated_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
                ->sum('total_cost');

            if ($allocCogs > 0 && $fifoCogs <= 0) {
                $fifoCogs = $allocCogs;
            }

            $grossProfit = round($salesAmount - $fifoCogs, 2);
            $grossMargin = $salesAmount > 0 ? round(($grossProfit / $salesAmount) * 100, 2) : 0.0;

            $rows[] = [
                'product_id' => $p->id,
                'product_name' => $p->name,
                'unit_name' => $p->unit?->name ?? 'Unit',
                'production_price' => (float) $p->production_price,
                'current_stock_qty' => $currentFifoQty,
                'current_stock_valuation' => $currentFifoValue,
                'delivered_qty' => $deliveredQty,
                'sales_amount' => $salesAmount,
                'fifo_cogs' => $fifoCogs,
                'gross_profit' => $grossProfit,
                'gross_margin' => $grossMargin,
            ];

            $totalDeliveredQty += $deliveredQty;
            $totalSales = round($totalSales + $salesAmount, 2);
            $totalCogs = round($totalCogs + $fifoCogs, 2);
            $totalGrossProfit = round($totalGrossProfit + $grossProfit, 2);
        }

        $overallMargin = $totalSales > 0 ? round(($totalGrossProfit / $totalSales) * 100, 2) : 0.0;

        return [
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'rows' => $rows,
            'total_delivered_qty' => $totalDeliveredQty,
            'total_sales' => $totalSales,
            'total_cogs' => $totalCogs,
            'total_gross_profit' => $totalGrossProfit,
            'overall_margin' => $overallMargin,
        ];
    }

    /**
     * Delivery-wise Profitability Report based on historical FIFO allocations
     */
    public function getDeliveryProfitability(?string $fromDate = null, ?string $toDate = null, ?int $customerId = null): array
    {
        $fromDate = $fromDate ?: Carbon::now()->startOfMonth()->format('Y-m-d');
        $toDate = $toDate ?: Carbon::now()->format('Y-m-d');

        $query = Delivery::with(['customer', 'order', 'items.product.unit'])
            ->whereBetween('delivery_date', [$fromDate, $toDate]);

        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        $deliveries = $query->orderBy('delivery_date', 'desc')->orderBy('id', 'desc')->get();

        $rows = [];
        $totalRevenue = 0.0;
        $totalCogs = 0.0;
        $totalGrossProfit = 0.0;

        foreach ($deliveries as $d) {
            $delRevenue = 0.0;
            $delCogs = 0.0;
            $itemRows = [];

            foreach ($d->items as $item) {
                $rev = (float) $item->total_price;
                $cogs = (float) $item->fifo_total_cost;
                $gp = round($rev - $cogs, 2);
                $margin = $rev > 0 ? round(($gp / $rev) * 100, 2) : 0.0;

                $delRevenue = round($delRevenue + $rev, 2);
                $delCogs = round($delCogs + $cogs, 2);

                $itemRows[] = [
                    'product_name' => $item->product?->name ?? 'Product',
                    'qty' => (float) $item->qty,
                    'unit_name' => $item->product?->unit?->short_code ?? '',
                    'sell_price' => (float) $item->sell_price,
                    'total_price' => $rev,
                    'fifo_unit_cost' => (float) $item->fifo_unit_cost,
                    'fifo_total_cost' => $cogs,
                    'gross_profit' => $gp,
                    'gross_margin' => $margin,
                ];
            }

            $delGrossProfit = round($delRevenue - $delCogs, 2);
            $delMargin = $delRevenue > 0 ? round(($delGrossProfit / $delRevenue) * 100, 2) : 0.0;

            $rows[] = [
                'delivery_id' => $d->id,
                'delivery_number' => $d->delivery_number,
                'delivery_date' => $d->delivery_date,
                'customer_name' => $d->customer?->company_name ?? 'Walk-in',
                'order_number' => $d->order?->order_number ?? 'N/A',
                'items' => $itemRows,
                'total_revenue' => $delRevenue,
                'total_cogs' => $delCogs,
                'gross_profit' => $delGrossProfit,
                'gross_margin' => $delMargin,
            ];

            $totalRevenue = round($totalRevenue + $delRevenue, 2);
            $totalCogs = round($totalCogs + $delCogs, 2);
            $totalGrossProfit = round($totalGrossProfit + $delGrossProfit, 2);
        }

        $overallMargin = $totalRevenue > 0 ? round(($totalGrossProfit / $totalRevenue) * 100, 2) : 0.0;

        return [
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'rows' => $rows,
            'total_revenue' => $totalRevenue,
            'total_cogs' => $totalCogs,
            'total_gross_profit' => $totalGrossProfit,
            'overall_margin' => $overallMargin,
        ];
    }
}

