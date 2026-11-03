<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. HR Advances Table (অগ্রিম বেতন / লোন)
        Schema::create('hr_advances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->date('advance_date');
            $table->string('deduct_month'); // e.g. 2026-11
            $table->text('reason')->nullable();
            $table->string('status')->default('pending'); // pending, approved, recovered, rejected
            $table->string('payment_method')->default('Cash');
            $table->string('approved_by')->nullable();
            $table->timestamps();
        });

        // 2. Add detailed deduction columns to hr_payrolls
        Schema::table('hr_payrolls', function (Blueprint $table) {
            $table->decimal('absence_deduction', 12, 2)->default(0)->after('bonus');
            $table->decimal('advance_deduction', 12, 2)->default(0)->after('absence_deduction');
            $table->decimal('penalty_deduction', 12, 2)->default(0)->after('advance_deduction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hr_payrolls', function (Blueprint $table) {
            $table->dropColumn(['absence_deduction', 'advance_deduction', 'penalty_deduction']);
        });
        Schema::dropIfExists('hr_advances');
    }
};

