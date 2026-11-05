<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Additive columns to productions table
        Schema::table('productions', function (Blueprint $table) {
            if (!Schema::hasColumn('productions', 'batch_number')) {
                $table->string('batch_number')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('productions', 'unit_cost_snapshot')) {
                $table->decimal('unit_cost_snapshot', 12, 2)->default(0)->after('batch_number');
            }
            if (!Schema::hasColumn('productions', 'total_cost')) {
                $table->decimal('total_cost', 14, 2)->default(0)->after('unit_cost_snapshot');
            }
        });

        // 2. Additive columns to delivery_items table
        Schema::table('delivery_items', function (Blueprint $table) {
            if (!Schema::hasColumn('delivery_items', 'fifo_unit_cost')) {
                $table->decimal('fifo_unit_cost', 12, 2)->default(0)->after('total_price');
            }
            if (!Schema::hasColumn('delivery_items', 'fifo_total_cost')) {
                $table->decimal('fifo_total_cost', 14, 2)->default(0)->after('fifo_unit_cost');
            }
            if (!Schema::hasColumn('delivery_items', 'gross_profit')) {
                $table->decimal('gross_profit', 14, 2)->default(0)->after('fifo_total_cost');
            }
        });

        // 3. Inventory Cost Layers (FIFO Batches)
        if (!Schema::hasTable('inventory_cost_layers')) {
            Schema::create('inventory_cost_layers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->foreignId('production_id')->nullable()->constrained('productions')->nullOnDelete();
                $table->string('batch_number');
                $table->date('received_date');
                $table->decimal('initial_qty', 12, 2);
                $table->decimal('remaining_qty', 12, 2);
                $table->decimal('unit_cost', 12, 2); // Production price snapshot at manufacturing completion
                $table->decimal('total_cost', 14, 2);
                $table->string('status')->default('active'); // active, exhausted, adjusted
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['product_id', 'status', 'received_date'], 'idx_fifo_lookup');
                $table->index('batch_number');
            });
        }

        // 4. Inventory Cost Allocations (Relates each delivery item to specific consumed FIFO layers)
        if (!Schema::hasTable('inventory_cost_allocations')) {
            Schema::create('inventory_cost_allocations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('delivery_item_id')->constrained('delivery_items')->cascadeOnDelete();
                $table->foreignId('inventory_cost_layer_id')->constrained('inventory_cost_layers')->cascadeOnDelete();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->decimal('allocated_qty', 12, 2);
                $table->decimal('unit_cost', 12, 2);
                $table->decimal('total_cost', 14, 2);
                $table->timestamp('allocated_at')->useCurrent();
                $table->timestamps();

                $table->index(['delivery_item_id', 'inventory_cost_layer_id'], 'idx_alloc_lookup');
            });
        }

        // 5. Inventory Cost Adjustments (Audit trail for cost or layer adjustments)
        if (!Schema::hasTable('inventory_cost_adjustments')) {
            Schema::create('inventory_cost_adjustments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('inventory_cost_layer_id')->constrained('inventory_cost_layers')->cascadeOnDelete();
                $table->string('adjustment_type'); // manual_adjustment, production_edit, reconciliation, damage_writeoff
                $table->decimal('qty_change', 12, 2);
                $table->decimal('cost_impact', 14, 2)->default(0);
                $table->text('reason');
                $table->foreignId('adjusted_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        // 6. Initialize existing products with opening FIFO valuation layer based on stocks.current_stock
        $now = now();
        $products = DB::table('products')->get();
        foreach ($products as $p) {
            $stock = DB::table('stocks')->where('product_id', $p->id)->first();
            $currentStock = $stock ? (float)$stock->current_stock : 0;
            $unitCost = (float)$p->production_price;

            if ($currentStock > 0) {
                $hasLayer = DB::table('inventory_cost_layers')->where('product_id', $p->id)->exists();
                if (!$hasLayer) {
                    DB::table('inventory_cost_layers')->insert([
                        'product_id' => $p->id,
                        'production_id' => null,
                        'batch_number' => 'INIT-STOCK-' . $p->id,
                        'received_date' => now()->toDateString(),
                        'initial_qty' => $currentStock,
                        'remaining_qty' => $currentStock,
                        'unit_cost' => $unitCost,
                        'total_cost' => round($currentStock * $unitCost, 2),
                        'status' => 'active',
                        'notes' => 'Initial stock opening valuation layer created at system integration',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_cost_adjustments');
        Schema::dropIfExists('inventory_cost_allocations');
        Schema::dropIfExists('inventory_cost_layers');

        Schema::table('delivery_items', function (Blueprint $table) {
            $table->dropColumn(['fifo_unit_cost', 'fifo_total_cost', 'gross_profit']);
        });

        Schema::table('productions', function (Blueprint $table) {
            $table->dropColumn(['batch_number', 'unit_cost_snapshot', 'total_cost']);
        });
    }
};

