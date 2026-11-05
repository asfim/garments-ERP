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
        // 1. Additive columns to existing account_vouchers table
        Schema::table('account_vouchers', function (Blueprint $table) {
            if (!Schema::hasColumn('account_vouchers', 'status')) {
                $table->string('status')->default('posted')->after('voucher_type'); // draft, posted, reversed
            }
            if (!Schema::hasColumn('account_vouchers', 'is_reversed')) {
                $table->boolean('is_reversed')->default(false)->after('status');
            }
            if (!Schema::hasColumn('account_vouchers', 'reversed_by_voucher_id')) {
                $table->foreignId('reversed_by_voucher_id')->nullable()->after('is_reversed')->constrained('account_vouchers')->nullOnDelete();
            }
            if (!Schema::hasColumn('account_vouchers', 'reversal_reason')) {
                $table->text('reversal_reason')->nullable()->after('reversed_by_voucher_id');
            }
            if (!Schema::hasColumn('account_vouchers', 'source_module')) {
                $table->string('source_module')->nullable()->after('reversal_reason');
            }
            if (!Schema::hasColumn('account_vouchers', 'source_id')) {
                $table->unsignedBigInteger('source_id')->nullable()->after('source_module');
            }
        });

        // 2. Multi-line Account Voucher Lines (Supports compound multi-debit/multi-credit journals)
        if (!Schema::hasTable('account_voucher_lines')) {
            Schema::create('account_voucher_lines', function (Blueprint $table) {
                $table->id();
                $table->foreignId('voucher_id')->constrained('account_vouchers')->cascadeOnDelete();
                $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();
                $table->enum('type', ['debit', 'credit']);
                $table->decimal('amount', 14, 2);
                $table->text('narration')->nullable();
                $table->timestamps();

                $table->index(['voucher_id', 'type']);
                $table->index(['account_id', 'type']);
            });
        }

        // 3. Accounting Source Links (Guarantees zero-duplicate posting from factory modules)
        if (!Schema::hasTable('accounting_source_links')) {
            Schema::create('accounting_source_links', function (Blueprint $table) {
                $table->id();
                $table->string('source_type'); // Model class, e.g. App\Models\Order
                $table->unsignedBigInteger('source_id');
                $table->string('event_type'); // sales_revenue, cogs, production_clearing, payroll_accrual, payroll_disbursement, advance_disbursement
                $table->foreignId('voucher_id')->constrained('account_vouchers')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['source_type', 'source_id', 'event_type'], 'source_link_unique');
            });
        }

        // 4. Accounting Audit Logs
        if (!Schema::hasTable('accounting_audit_logs')) {
            Schema::create('accounting_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('voucher_id')->nullable()->constrained('account_vouchers')->nullOnDelete();
                $table->string('action'); // posted, reversed, draft_created, balance_recalculated
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->text('details')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
            });
        }

        // 5. Accounting Reversals (Formal contra audit tracking)
        if (!Schema::hasTable('accounting_reversals')) {
            Schema::create('accounting_reversals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('original_voucher_id')->constrained('account_vouchers')->cascadeOnDelete();
                $table->foreignId('reversal_voucher_id')->constrained('account_vouchers')->cascadeOnDelete();
                $table->text('reason');
                $table->foreignId('reversed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('reversed_at')->useCurrent();
                $table->timestamps();

                $table->unique(['original_voucher_id', 'reversal_voucher_id'], 'reversal_pair_unique');
            });
        }

        // 6. Accounting Mappings (Configurable mapping table)
        if (!Schema::hasTable('accounting_mappings')) {
            Schema::create('accounting_mappings', function (Blueprint $table) {
                $table->id();
                $table->string('mapping_key')->unique();
                $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();
                $table->string('title');
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 7. Seed missing required accounts safely
        $now = now();
        $requiredAccounts = [
            // Finished Goods Inventory (Asset)
            ['account_code' => '1040', 'name' => 'Finished Goods Inventory (তৈরি পোশাক মজুদ)', 'type' => 'asset', 'category' => 'Inventory', 'opening_balance' => 0, 'current_balance' => 0, 'is_system' => true],
            // Raw Materials Inventory (Asset)
            ['account_code' => '1041', 'name' => 'Raw Materials Inventory (কাঁচামাল মজুদ)', 'type' => 'asset', 'category' => 'Inventory', 'opening_balance' => 0, 'current_balance' => 0, 'is_system' => true],
            // Work In Progress (Asset)
            ['account_code' => '1042', 'name' => 'Work in Progress - WIP (চলতি উৎপাদন মজুদ)', 'type' => 'asset', 'category' => 'Inventory', 'opening_balance' => 0, 'current_balance' => 0, 'is_system' => true],
            // Employee Advance Receivable (Asset)
            ['account_code' => '1060', 'name' => 'Employee Advance Receivable (কর্মচারীদের অগ্রিম ঋণ)', 'type' => 'asset', 'category' => 'Accounts Receivable', 'opening_balance' => 0, 'current_balance' => 0, 'is_system' => true],
            // Salary Payable (Liability)
            ['account_code' => '2010', 'name' => 'Salary & Wages Payable (প্রদেয় বেতন ও মজুরি)', 'type' => 'liability', 'category' => 'Current Liability', 'opening_balance' => 0, 'current_balance' => 0, 'is_system' => true],
            // Other Payables (Liability)
            ['account_code' => '2030', 'name' => 'Other Accounts Payable (অন্যান্য প্রদেয় দায়)', 'type' => 'liability', 'category' => 'Current Liability', 'opening_balance' => 0, 'current_balance' => 0, 'is_system' => false],
            // Product COGS (Cost of Goods Sold / Expense)
            ['account_code' => '5100', 'name' => 'Cost of Goods Sold - COGS (বিক্রিত পণ্যের ব্যয়)', 'type' => 'expense', 'category' => 'Cost of Goods Sold', 'opening_balance' => 0, 'current_balance' => 0, 'is_system' => true],
            // Manufacturing Absorption / Production Clearing
            ['account_code' => '5200', 'name' => 'Production Cost Absorption Clearing (উৎপাদন ব্যয় সমন্বয়)', 'type' => 'expense', 'category' => 'Direct Expense', 'opening_balance' => 0, 'current_balance' => 0, 'is_system' => true],
        ];

        foreach ($requiredAccounts as $acc) {
            $exists = DB::table('accounts')->where('account_code', $acc['account_code'])->exists();
            if (!$exists) {
                $acc['created_at'] = $now;
                $acc['updated_at'] = $now;
                DB::table('accounts')->insert($acc);
            }
        }

        // 8. Seed default accounting mappings
        $mappingDefs = [
            'sales' => ['code' => '4001', 'title' => 'Product Sales Revenue', 'desc' => 'Credited when orders or shipments are fulfilled'],
            'cogs' => ['code' => '5100', 'title' => 'Cost of Goods Sold (COGS)', 'desc' => 'Debited on delivery dispatch via FIFO allocation'],
            'finished_goods' => ['code' => '1040', 'title' => 'Finished Goods Inventory', 'desc' => 'Debited on production receipt, credited on delivery shipment'],
            'raw_materials' => ['code' => '1041', 'title' => 'Raw Materials Inventory', 'desc' => 'Debited on inventory purchase receipts'],
            'production_absorption' => ['code' => '5200', 'title' => 'Production Cost Absorption Clearing', 'desc' => 'Credited on production completion to absorb standard unit costs'],
            'payroll_expense' => ['code' => '5003', 'title' => 'Salary & Wages Expense', 'desc' => 'Debited on monthly payroll generation accrual'],
            'payroll_payable' => ['code' => '2010', 'title' => 'Salary & Wages Payable', 'desc' => 'Credited on accrual, debited on payment disbursement'],
            'advance_receivable' => ['code' => '1060', 'title' => 'Employee Advance Receivable', 'desc' => 'Debited on loan disbursement, credited on payroll deduction recovery'],
            'accounts_receivable' => ['code' => '1050', 'title' => 'Accounts Receivable (Customers)', 'desc' => 'Debited on credit sales, credited on customer payment collection'],
            'accounts_payable' => ['code' => '2001', 'title' => 'Accounts Payable (Suppliers)', 'desc' => 'Credited on supplier bill, debited on payment to supplier'],
            'cash_default' => ['code' => '1001', 'title' => 'Default Cash Account', 'desc' => 'Default liquid account for cash operations'],
        ];

        foreach ($mappingDefs as $key => $def) {
            $accId = DB::table('accounts')->where('account_code', $def['code'])->value('id');
            if ($accId && !DB::table('accounting_mappings')->where('mapping_key', $key)->exists()) {
                DB::table('accounting_mappings')->insert([
                    'mapping_key' => $key,
                    'account_id' => $accId,
                    'title' => $def['title'],
                    'description' => $def['desc'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // 9. Backfill voucher lines for existing vouchers so historical records have voucher lines
        $existingVouchers = DB::table('account_vouchers')->get();
        foreach ($existingVouchers as $v) {
            $hasLines = DB::table('account_voucher_lines')->where('voucher_id', $v->id)->exists();
            if (!$hasLines && $v->debit_account_id && $v->credit_account_id) {
                // Debit line
                DB::table('account_voucher_lines')->insert([
                    'voucher_id' => $v->id,
                    'account_id' => $v->debit_account_id,
                    'type' => 'debit',
                    'amount' => $v->amount,
                    'narration' => $v->notes ?? 'Debit entry',
                    'created_at' => $v->created_at,
                    'updated_at' => $v->updated_at,
                ]);
                // Credit line
                DB::table('account_voucher_lines')->insert([
                    'voucher_id' => $v->id,
                    'account_id' => $v->credit_account_id,
                    'type' => 'credit',
                    'amount' => $v->amount,
                    'narration' => $v->notes ?? 'Credit entry',
                    'created_at' => $v->created_at,
                    'updated_at' => $v->updated_at,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_mappings');
        Schema::dropIfExists('accounting_reversals');
        Schema::dropIfExists('accounting_audit_logs');
        Schema::dropIfExists('accounting_source_links');
        Schema::dropIfExists('account_voucher_lines');

        Schema::table('account_vouchers', function (Blueprint $table) {
            $table->dropForeign(['reversed_by_voucher_id']);
            $table->dropColumn(['status', 'is_reversed', 'reversed_by_voucher_id', 'reversal_reason', 'source_module', 'source_id']);
        });
    }
};

