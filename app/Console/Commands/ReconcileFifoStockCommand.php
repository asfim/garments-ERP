<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Stock;
use App\Models\InventoryCostLayer;
use App\Services\FifoInventoryService;

class ReconcileFifoStockCommand extends Command
{
    protected $signature = 'stock:reconcile-fifo {--fix : Automatically adjust discrepancy with an adjustment layer}';
    protected $description = 'Reconcile physical stock balances with FIFO inventory cost layers';

    public function handle(FifoInventoryService $fifoService): int
    {
        $this->info("Starting FIFO Inventory & Stock Reconciliation Audit...");

        $products = Product::with(['unit', 'stock'])->orderBy('name')->get();
        $tableRows = [];
        $discrepanciesCount = 0;

        foreach ($products as $p) {
            $stockQty = $p->stock ? (float)$p->stock->current_stock : 0.0;
            $fifoQty = (float) InventoryCostLayer::where('product_id', $p->id)
                ->where('status', 'active')
                ->sum('remaining_qty');

            $diff = round($stockQty - $fifoQty, 4);
            $status = abs($diff) < 0.0001 ? '<fg=green>BALANCED</>' : '<fg=red>MISMATCH</>';

            if (abs($diff) >= 0.0001) {
                $discrepanciesCount++;
                if ($this->option('fix')) {
                    if ($diff > 0) {
                        // Create adjustment layer for missing balance
                        $fifoService->createCostLayer(
                            $p,
                            $diff,
                            (float)$p->production_price,
                            'ADJ-RECON-' . date('Ymd'),
                            now()->toDateString(),
                            null,
                            'Auto-reconciliation layer created by stock:reconcile-fifo'
                        );
                        $status = '<fg=yellow>FIXED (+Layer)</>';
                    }
                }
            }

            $tableRows[] = [
                $p->id,
                $p->name,
                $p->unit?->short_code ?? '',
                number_format($stockQty, 2),
                number_format($fifoQty, 2),
                number_format($diff, 2),
                $status,
            ];
        }

        $this->table(
            ['ID', 'Product Name', 'Unit', 'Stock Qty', 'FIFO Layer Qty', 'Difference', 'Audit Status'],
            $tableRows
        );

        if ($discrepanciesCount === 0) {
            $this->info("All products are perfectly balanced with FIFO inventory cost layers (0 discrepancies).");
        } else {
            if ($this->option('fix')) {
                $this->info("All {$discrepanciesCount} discrepancies have been resolved.");
            } else {
                $this->warn("Found {$discrepanciesCount} product(s) with quantity discrepancies. Run with --fix to reconcile.");
            }
        }

        return 0;
    }
}

