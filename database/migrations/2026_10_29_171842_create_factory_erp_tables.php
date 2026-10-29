<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Company Settings (for PDF headers, invoices)
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->default('Turkey Bangla Garments Ltd');
            $table->string('logo')->nullable();
            $table->string('address')->default('Tongi Industrial Area, Gazipur, Dhaka');
            $table->string('phone')->default('+880 1712-345678');
            $table->string('email')->nullable()->default('info@turkeybangla.com');
            $table->string('website')->nullable()->default('turkeybangla.com');
            $table->timestamps();
        });

        // 2. Units (dynamic dropdown)
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Pcs, Dozen, Box, Kg, Meter
            $table->string('short_code')->nullable();
            $table->timestamps();
        });

        // 3. Products
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade');
            $table->decimal('production_price', 12, 2)->default(0);
            $table->decimal('sell_price', 12, 2)->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 4. Customers
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('phone');
            $table->text('address')->nullable();
            $table->string('contact_person')->nullable();
            $table->timestamps();
        });

        // 5. Orders
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // ORD-2026-0001
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->date('order_date');
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->decimal('paid_amount', 14, 2)->default(0);
            $table->decimal('due_amount', 14, 2)->default(0);
            $table->enum('delivery_status', ['pending', 'partial', 'delivered'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 6. Order Items
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->decimal('qty', 12, 2);
            $table->decimal('sell_price', 12, 2);
            $table->decimal('total_price', 14, 2);
            $table->decimal('delivered_qty', 12, 2)->default(0);
            $table->decimal('due_qty', 12, 2)->default(0);
            $table->timestamps();
        });

        // 7. Productions
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade');
            $table->date('production_date');
            $table->decimal('target_qty', 12, 2)->default(0);
            $table->decimal('produced_qty', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 8. Deliveries
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('delivery_number')->unique(); // DEL-2026-0001
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->date('delivery_date');
            $table->decimal('total_delivered_amount', 14, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 9. Delivery Items
        Schema::create('delivery_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained('deliveries')->onDelete('cascade');
            $table->foreignId('order_item_id')->constrained('order_items')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->decimal('qty', 12, 2);
            $table->decimal('sell_price', 12, 2);
            $table->decimal('total_price', 14, 2);
            $table->timestamps();
        });

        // 10. Stocks (Tracks inventory balance per product, auto-updated + manual adjustments)
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->unique()->constrained('products')->onDelete('cascade');
            $table->decimal('current_stock', 14, 2)->default(0);
            $table->decimal('total_produced', 14, 2)->default(0);
            $table->decimal('total_delivered', 14, 2)->default(0);
            $table->decimal('manual_adjusted', 14, 2)->default(0);
            $table->timestamps();
        });

        // 11. Stock Logs (History of stock movements)
        Schema::create('stock_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->enum('type', ['production', 'delivery', 'manual_add', 'manual_subtract']);
            $table->decimal('qty', 12, 2);
            $table->decimal('previous_stock', 14, 2);
            $table->decimal('new_stock', 14, 2);
            $table->string('reference_id')->nullable(); // e.g. ORD-001 or PROD-001
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_logs');
        Schema::dropIfExists('stocks');
        Schema::dropIfExists('delivery_items');
        Schema::dropIfExists('deliveries');
        Schema::dropIfExists('productions');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('products');
        Schema::dropIfExists('units');
        Schema::dropIfExists('company_settings');
    }
};
