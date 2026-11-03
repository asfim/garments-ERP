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
        // 1. Chart of Accounts & Liquid Accounts (Cash, Bank, Income, Expense, etc.)
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_code')->unique(); // 1001, 1002, 4001, etc.
            $table->string('name');
            $table->enum('type', ['asset', 'liability', 'equity', 'income', 'expense']);
            $table->string('category')->default('General'); // Cash in Hand, Bank Account, Mobile Banking, Operating Income, Direct Expense, etc.
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('routing_number')->nullable();
            $table->decimal('opening_balance', 14, 2)->default(0);
            $table->decimal('current_balance', 14, 2)->default(0);
            $table->boolean('is_system')->default(false);
            $table->string('status')->default('active'); // active, inactive
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Account Vouchers (Income, Expense, Transfer, Journal)
        Schema::create('account_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_no')->unique(); // INC-2026-0001, EXP-2026-0001, TRF-2026-0001
            $table->enum('voucher_type', ['income', 'expense', 'transfer', 'journal']);
            $table->date('voucher_date');
            $table->decimal('amount', 14, 2);
            $table->foreignId('debit_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('credit_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->string('party_name')->nullable(); // Payer or Payee name
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('hr_employees')->nullOnDelete();
            $table->string('payment_method')->default('cash'); // cash, bank, cheque, bkash, nagad, rocket, other
            $table->string('reference_no')->nullable(); // Cheque no, TrxID, invoice ref
            $table->date('cheque_date')->nullable();
            $table->string('attachment')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 3. Account Transactions (Double-Entry General Ledger lines)
        Schema::create('account_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voucher_id')->constrained('account_vouchers')->cascadeOnDelete();
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->date('transaction_date');
            $table->enum('type', ['debit', 'credit']);
            $table->decimal('amount', 14, 2);
            $table->decimal('balance_after', 14, 2)->default(0);
            $table->text('narration')->nullable();
            $table->string('reference_no')->nullable();
            $table->timestamps();
        });

        // Seed initial standard Chart of Accounts for Garments / Factory ERP
        $now = now();
        $initialAccounts = [
            // Assets
            ['account_code' => '1001', 'name' => 'Cash in Hand (প্রধান ক্যাশ)', 'type' => 'asset', 'category' => 'Cash in Hand', 'opening_balance' => 0, 'current_balance' => 0, 'is_system' => true],
            ['account_code' => '1002', 'name' => 'Petty Cash (পেটি ক্যাশ)', 'type' => 'asset', 'category' => 'Cash in Hand', 'opening_balance' => 0, 'current_balance' => 0, 'is_system' => false],
            ['account_code' => '1010', 'name' => 'Dutch-Bangla Bank Ltd (DBBL)', 'type' => 'asset', 'category' => 'Bank Account', 'bank_name' => 'DBBL', 'account_number' => '126.110.005432', 'branch_name' => 'Uttara Branch', 'opening_balance' => 0, 'current_balance' => 0, 'is_system' => false],
            ['account_code' => '1011', 'name' => 'Islami Bank Bangladesh Ltd (IBBL)', 'type' => 'asset', 'category' => 'Bank Account', 'bank_name' => 'IBBL', 'account_number' => '2050.210.009876', 'branch_name' => 'Gazipur Branch', 'opening_balance' => 0, 'current_balance' => 0, 'is_system' => false],
            ['account_code' => '1020', 'name' => 'bKash Merchant Account', 'type' => 'asset', 'category' => 'Mobile Banking', 'account_number' => '01712-345678', 'opening_balance' => 0, 'current_balance' => 0, 'is_system' => false],
            ['account_code' => '1021', 'name' => 'Nagad Merchant Account', 'type' => 'asset', 'category' => 'Mobile Banking', 'account_number' => '01812-345678', 'opening_balance' => 0, 'current_balance' => 0, 'is_system' => false],
            ['account_code' => '1050', 'name' => 'Accounts Receivable (কাস্টমার বাকি/প্রাপ্য)', 'type' => 'asset', 'category' => 'Accounts Receivable', 'opening_balance' => 0, 'current_balance' => 0, 'is_system' => true],
            
            // Liabilities
            ['account_code' => '2001', 'name' => 'Accounts Payable (সাপ্লায়ার বাকি/প্রদেয়)', 'type' => 'liability', 'category' => 'Accounts Payable', 'opening_balance' => 0, 'current_balance' => 0, 'is_system' => true],
            ['account_code' => '2010', 'name' => 'Accrued Salaries & Wages (বকেয়া বেতন)', 'type' => 'liability', 'category' => 'Current Liability', 'opening_balance' => 0, 'current_balance' => 0, 'is_system' => false],
            ['account_code' => '2020', 'name' => 'Short-Term Bank Loan (ব্যাংক ঋণ)', 'type' => 'liability', 'category' => 'Current Liability', 'opening_balance' => 0, 'current_balance' => 0, 'is_system' => false],
            
            // Equity
            ['account_code' => '3001', 'name' => 'Owner Capital (মালিকের মূলধন)', 'type' => 'equity', 'category' => 'Capital', 'opening_balance' => 0, 'current_balance' => 0, 'is_system' => true],
            ['account_code' => '3002', 'name' => 'Retained Earnings (জমা মুনাফা)', 'type' => 'equity', 'category' => 'Retained Earnings', 'opening_balance' => 0, 'current_balance' => 0, 'is_system' => false],

            // Income / Revenue
            ['account_code' => '4001', 'name' => 'Garments Sales Revenue (পোশাক বিক্রয় আয়)', 'type' => 'income', 'category' => 'Operating Income', 'opening_balance' => 0, 'current_balance' => 0, 'is_system' => true],
            ['account_code' => '4002', 'name' => 'Scrap & Waste Fabric Sales (ঝুট বিক্রয়)', 'type' => 'income', 'category' => 'Other Income', 'opening_balance' => 0, 'current_balance' => 0, 'is_system' => false],
            ['account_code' => '4003', 'name' => 'Sample & Development Income (স্যাম্পল ফি)', 'type' => 'income', 'category' => 'Service Income', 'opening_balance' => 0, 'current_balance' => 0, 'is_system' => false],
            ['account_code' => '4004', 'name' => 'Printing & Embroidery Income (প্রিন্টিং আয়)', 'type' => 'income', 'category' => 'Service Income', 'opening_balance' => 0, 'current_balance' => 0, 'is_system' => false],
            ['account_code' => '4099', 'name' => 'Miscellaneous Income (অন্যান্য বিবিধ আয়)', 'type' => 'income', 'category' => 'Other Income', 'opening_balance' => 0, 'current_balance' => 0, 'is_system' => false],

            // Expenses
            ['account_code' => '5001', 'name' => 'Raw Materials & Fabric Purchase (কাঁচামাল ও সুতা ক্রয়)', 'type' => 'expense', 'category' => 'Direct Expense', 'opening_balance' => 0, 'current_balance' => 0, 'is_system' => true],
            ['account_code' => '5002', 'name' => 'Accessories & Trims Purchase (এক্সেসরিজ ও বোতাম)', 'type' => 'expense', 'category' => 'Direct Expense', 'opening_balance' => 0, 'current_balance' => 0, 'is_system' => false],
            ['account_code' => '5003', 'name' => 'Employee Salaries & Wages (শ্রমিক-কর্মচারীদের বেতন)', 'type' => 'expense', 'category' => 'Operating Expense', 'opening_balance' => 0, 'current_balance' => 0, 'is_system' => true],
            ['account_code' => '5004', 'name' => 'Factory Rent (কারখানা ও অফিস ভাড়া)', 'type' => 'expense', 'category' => 'Operating Expense', 'opening_balance' => 0, 'current_balance' => 0, 'is_system' => false],
            ['account_code' => '5005', 'name' => 'Electricity & Generator Fuel (বিদ্যুৎ ও জ্বালানি বিল)', 'type' => 'expense', 'category' => 'Factory Overhead', 'opening_balance' => 0, 'current_balance' => 0, 'is_system' => false],
            ['account_code' => '5006', 'name' => 'Gas & Utility Bills (গ্যাস ও ইউটিলিটি)', 'type' => 'expense', 'category' => 'Factory Overhead', 'opening_balance' => 0, 'current_balance' => 0, 'is_system' => false],
            ['account_code' => '5007', 'name' => 'Machine Maintenance & Repairs (মেশিনারি মেরামত)', 'type' => 'expense', 'category' => 'Factory Overhead', 'opening_balance' => 0, 'current_balance' => 0, 'is_system' => false],
            ['account_code' => '5008', 'name' => 'Transport & Logistics (মালামাল পরিবহন ও ডেলিভারি)', 'type' => 'expense', 'category' => 'Selling & Distribution', 'opening_balance' => 0, 'current_balance' => 0, 'is_system' => false],
            ['account_code' => '5009', 'name' => 'Office Tea & Entertainment (অফিস নাস্তা ও আপ্যায়ন)', 'type' => 'expense', 'category' => 'Administrative Expense', 'opening_balance' => 0, 'current_balance' => 0, 'is_system' => false],
            ['account_code' => '5010', 'name' => 'Packaging & Cartons (প্যাকিং ও কার্টুন খরচ)', 'type' => 'expense', 'category' => 'Direct Expense', 'opening_balance' => 0, 'current_balance' => 0, 'is_system' => false],
            ['account_code' => '5011', 'name' => 'Mobile & Internet Bills (মোবাইল ও ইন্টারনেট)', 'type' => 'expense', 'category' => 'Administrative Expense', 'opening_balance' => 0, 'current_balance' => 0, 'is_system' => false],
            ['account_code' => '5099', 'name' => 'Miscellaneous Expense (অন্যান্য সাধারণ খরচ)', 'type' => 'expense', 'category' => 'Administrative Expense', 'opening_balance' => 0, 'current_balance' => 0, 'is_system' => false],
        ];

        foreach ($initialAccounts as $acc) {
            $acc['created_at'] = $now;
            $acc['updated_at'] = $now;
            DB::table('accounts')->insert($acc);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_transactions');
        Schema::dropIfExists('account_vouchers');
        Schema::dropIfExists('accounts');
    }
};

