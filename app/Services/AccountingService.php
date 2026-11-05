<?php

namespace App\Services;

use App\Models\Account;
use App\Models\AccountVoucher;
use App\Models\AccountTransaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AccountingService
{
    /**
     * Record an Income Voucher (Debit Liquid Account, Credit Income Head)
     */
    public function recordIncome(array $data, ?string $attachmentPath = null): AccountVoucher
    {
        return DB::transaction(function () use ($data, $attachmentPath) {
            $voucherNo = $data['voucher_no'] ?? AccountVoucher::generateVoucherNo('income');
            $depositAccountId = $data['deposit_account_id']; // Asset (Cash/Bank)
            $incomeAccountId = $data['income_account_id'];   // Income head
            $amount = (float) $data['amount'];
            $date = $data['voucher_date'] ?? date('Y-m-d');

            $voucher = AccountVoucher::create([
                'voucher_no' => $voucherNo,
                'voucher_type' => 'income',
                'voucher_date' => $date,
                'amount' => $amount,
                'debit_account_id' => $depositAccountId,
                'credit_account_id' => $incomeAccountId,
                'party_name' => $data['party_name'] ?? null,
                'customer_id' => $data['customer_id'] ?? null,
                'payment_method' => $data['payment_method'] ?? 'cash',
                'reference_no' => $data['reference_no'] ?? null,
                'cheque_date' => $data['cheque_date'] ?? null,
                'attachment' => $attachmentPath,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id() ?? null,
            ]);

            // 1. Debit Entry: Bank/Cash Asset increases
            AccountTransaction::create([
                'voucher_id' => $voucher->id,
                'account_id' => $depositAccountId,
                'transaction_date' => $date,
                'type' => 'debit',
                'amount' => $amount,
                'narration' => $data['notes'] ?: ("Income received via " . ucfirst($data['payment_method'] ?? 'cash')),
                'reference_no' => $data['reference_no'] ?? null,
            ]);

            // 2. Credit Entry: Income Head increases
            AccountTransaction::create([
                'voucher_id' => $voucher->id,
                'account_id' => $incomeAccountId,
                'transaction_date' => $date,
                'type' => 'credit',
                'amount' => $amount,
                'narration' => $data['notes'] ?: ("Income credited from " . ($data['party_name'] ?? 'Payer')),
                'reference_no' => $data['reference_no'] ?? null,
            ]);

            // Recalculate balances
            $depositAcc = Account::find($depositAccountId);
            $depositAcc?->recalculateBalance();

            $incomeAcc = Account::find($incomeAccountId);
            $incomeAcc?->recalculateBalance();

            return $voucher;
        });
    }

    /**
     * Record an Expense Voucher (Debit Expense Head, Credit Liquid Account)
     */
    public function recordExpense(array $data, ?string $attachmentPath = null): AccountVoucher
    {
        return DB::transaction(function () use ($data, $attachmentPath) {
            $voucherNo = $data['voucher_no'] ?? AccountVoucher::generateVoucherNo('expense');
            $expenseAccountId = $data['expense_account_id']; // Expense Head
            $paidFromAccountId = $data['paid_from_account_id']; // Asset (Cash/Bank)
            $amount = (float) $data['amount'];
            $date = $data['voucher_date'] ?? date('Y-m-d');

            $voucher = AccountVoucher::create([
                'voucher_no' => $voucherNo,
                'voucher_type' => 'expense',
                'voucher_date' => $date,
                'amount' => $amount,
                'debit_account_id' => $expenseAccountId,
                'credit_account_id' => $paidFromAccountId,
                'party_name' => $data['party_name'] ?? null,
                'supplier_id' => $data['supplier_id'] ?? null,
                'employee_id' => $data['employee_id'] ?? null,
                'payment_method' => $data['payment_method'] ?? 'cash',
                'reference_no' => $data['reference_no'] ?? null,
                'cheque_date' => $data['cheque_date'] ?? null,
                'attachment' => $attachmentPath,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id() ?? null,
            ]);

            // 1. Debit Entry: Expense increases
            AccountTransaction::create([
                'voucher_id' => $voucher->id,
                'account_id' => $expenseAccountId,
                'transaction_date' => $date,
                'type' => 'debit',
                'amount' => $amount,
                'narration' => $data['notes'] ?: ("Expense incurred to " . ($data['party_name'] ?? 'Payee')),
                'reference_no' => $data['reference_no'] ?? null,
            ]);

            // 2. Credit Entry: Bank/Cash Asset decreases
            AccountTransaction::create([
                'voucher_id' => $voucher->id,
                'account_id' => $paidFromAccountId,
                'transaction_date' => $date,
                'type' => 'credit',
                'amount' => $amount,
                'narration' => $data['notes'] ?: ("Expense paid via " . ucfirst($data['payment_method'] ?? 'cash')),
                'reference_no' => $data['reference_no'] ?? null,
            ]);

            // Recalculate balances
            $expenseAcc = Account::find($expenseAccountId);
            $expenseAcc?->recalculateBalance();

            $paidFromAcc = Account::find($paidFromAccountId);
            $paidFromAcc?->recalculateBalance();

            return $voucher;
        });
    }

    /**
     * Record a Fund Transfer (Contra Entry: Debit Receiving Account, Credit Sending Account)
     */
    public function recordTransfer(array $data): AccountVoucher
    {
        return DB::transaction(function () use ($data) {
            $voucherNo = $data['voucher_no'] ?? AccountVoucher::generateVoucherNo('transfer');
            $fromAccountId = $data['from_account_id']; // Sending (Asset decreases - Credit)
            $toAccountId = $data['to_account_id'];     // Receiving (Asset increases - Debit)
            $amount = (float) $data['amount'];
            $date = $data['voucher_date'] ?? date('Y-m-d');

            $voucher = AccountVoucher::create([
                'voucher_no' => $voucherNo,
                'voucher_type' => 'transfer',
                'voucher_date' => $date,
                'amount' => $amount,
                'debit_account_id' => $toAccountId,
                'credit_account_id' => $fromAccountId,
                'party_name' => 'Internal Transfer',
                'payment_method' => $data['payment_method'] ?? 'bank_transfer',
                'reference_no' => $data['reference_no'] ?? null,
                'notes' => $data['notes'] ?? 'Internal fund transfer',
                'created_by' => auth()->id() ?? null,
            ]);

            // 1. Debit Entry: Receiving Account increases
            AccountTransaction::create([
                'voucher_id' => $voucher->id,
                'account_id' => $toAccountId,
                'transaction_date' => $date,
                'type' => 'debit',
                'amount' => $amount,
                'narration' => "Fund received from A/C #" . Account::find($fromAccountId)?->account_code . " - " . ($data['notes'] ?? ''),
                'reference_no' => $data['reference_no'] ?? null,
            ]);

            // 2. Credit Entry: Sending Account decreases
            AccountTransaction::create([
                'voucher_id' => $voucher->id,
                'account_id' => $fromAccountId,
                'transaction_date' => $date,
                'type' => 'credit',
                'amount' => $amount,
                'narration' => "Fund transferred to A/C #" . Account::find($toAccountId)?->account_code . " - " . ($data['notes'] ?? ''),
                'reference_no' => $data['reference_no'] ?? null,
            ]);

            // Recalculate balances
            Account::find($fromAccountId)?->recalculateBalance();
            Account::find($toAccountId)?->recalculateBalance();

            return $voucher;
        });
    }

    /**
     * Delete Voucher and revert account balances
     */
    public function deleteVoucher(AccountVoucher $voucher): void
    {
        DB::transaction(function () use ($voucher) {
            $debitAccId = $voucher->debit_account_id;
            $creditAccId = $voucher->credit_account_id;

            // Delete ledger entries
            $voucher->transactions()->delete();
            $voucher->delete();

            // Recalculate balances
            if ($debitAccId) {
                Account::find($debitAccId)?->recalculateBalance();
            }
            if ($creditAccId) {
                Account::find($creditAccId)?->recalculateBalance();
            }
        });
    }

    /**
     * Get Ledger Statement with running balance for an Account
     */
    public function getAccountLedger(Account $account, ?string $fromDate = null, ?string $toDate = null): array
    {
        $fromDate = $fromDate ?: Carbon::now()->startOfMonth()->format('Y-m-d');
        $toDate = $toDate ?: Carbon::now()->format('Y-m-d');

        // Calculate opening balance before $fromDate
        $priorDebits = (float) $account->transactions()
            ->whereDate('transaction_date', '<', $fromDate)
            ->where('type', 'debit')
            ->sum('amount');

        $priorCredits = (float) $account->transactions()
            ->whereDate('transaction_date', '<', $fromDate)
            ->where('type', 'credit')
            ->sum('amount');

        $initialOpening = (float) $account->opening_balance;

        if (in_array($account->type, ['asset', 'expense'])) {
            $periodOpeningBalance = $initialOpening + $priorDebits - $priorCredits;
        } else {
            $periodOpeningBalance = $initialOpening + $priorCredits - $priorDebits;
        }

        // Get transactions in the date range
        $transactions = $account->transactions()
            ->with(['voucher'])
            ->whereBetween('transaction_date', [$fromDate, $toDate])
            ->orderBy('transaction_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $running = $periodOpeningBalance;
        $ledgerEntries = [];

        foreach ($transactions as $tx) {
            $dr = $tx->type === 'debit' ? (float)$tx->amount : 0.0;
            $cr = $tx->type === 'credit' ? (float)$tx->amount : 0.0;

            if (in_array($account->type, ['asset', 'expense'])) {
                $running += ($dr - $cr);
            } else {
                $running += ($cr - $dr);
            }

            $ledgerEntries[] = [
                'id' => $tx->id,
                'date' => $tx->transaction_date->format('Y-m-d'),
                'voucher_no' => $tx->voucher?->voucher_no ?? 'N/A',
                'voucher_type' => $tx->voucher?->voucher_type ?? 'N/A',
                'voucher_id' => $tx->voucher?->id,
                'narration' => $tx->narration,
                'reference_no' => $tx->reference_no,
                'debit' => $dr,
                'credit' => $cr,
                'running_balance' => $running,
            ];
        }

        return [
            'account' => $account,
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'opening_balance' => $periodOpeningBalance,
            'closing_balance' => $running,
            'entries' => $ledgerEntries,
            'total_debit' => collect($ledgerEntries)->sum('debit'),
            'total_credit' => collect($ledgerEntries)->sum('credit'),
        ];
    }

    /**
     * Profit and Loss calculation for a period
     */
    public function getProfitAndLoss(?string $fromDate = null, ?string $toDate = null): array
    {
        $fromDate = $fromDate ?: Carbon::now()->startOfMonth()->format('Y-m-d');
        $toDate = $toDate ?: Carbon::now()->format('Y-m-d');

        // Income accounts
        $incomeAccounts = Account::where('type', 'income')->get();
        $incomeDetails = [];
        $totalIncome = 0.0;

        foreach ($incomeAccounts as $acc) {
            // Net income is Credit minus Debit
            $credits = (float) $acc->transactions()
                ->whereBetween('transaction_date', [$fromDate, $toDate])
                ->where('type', 'credit')
                ->sum('amount');
            $debits = (float) $acc->transactions()
                ->whereBetween('transaction_date', [$fromDate, $toDate])
                ->where('type', 'debit')
                ->sum('amount');
            $net = $credits - $debits;
            if ($net != 0 || $credits > 0) {
                $incomeDetails[] = [
                    'account' => $acc,
                    'amount' => $net,
                ];
                $totalIncome += $net;
            }
        }

        // Expense accounts
        $expenseAccounts = Account::where('type', 'expense')->get();
        $expenseDetails = [];
        $totalExpense = 0.0;

        foreach ($expenseAccounts as $acc) {
            // Net expense is Debit minus Credit
            $debits = (float) $acc->transactions()
                ->whereBetween('transaction_date', [$fromDate, $toDate])
                ->where('type', 'debit')
                ->sum('amount');
            $credits = (float) $acc->transactions()
                ->whereBetween('transaction_date', [$fromDate, $toDate])
                ->where('type', 'credit')
                ->sum('amount');
            $net = $debits - $credits;
            if ($net != 0 || $debits > 0) {
                $expenseDetails[] = [
                    'account' => $acc,
                    'amount' => $net,
                ];
                $totalExpense += $net;
            }
        }

        $netProfit = $totalIncome - $totalExpense;

        return [
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'income_details' => $incomeDetails,
            'total_income' => $totalIncome,
            'expense_details' => $expenseDetails,
            'total_expense' => $totalExpense,
            'net_profit' => $netProfit,
        ];
    }

    /**
     * Trial Balance calculation
     */
    public function getTrialBalance(?string $asOfDate = null): array
    {
        $asOfDate = $asOfDate ?: Carbon::now()->format('Y-m-d');
        $accounts = Account::orderBy('account_code')->get();

        $rows = [];
        $totalDr = 0.0;
        $totalCr = 0.0;

        foreach ($accounts as $acc) {
            $totalDebit = (float) $acc->transactions()
                ->whereDate('transaction_date', '<=', $asOfDate)
                ->where('type', 'debit')
                ->sum('amount');

            $totalCredit = (float) $acc->transactions()
                ->whereDate('transaction_date', '<=', $asOfDate)
                ->where('type', 'credit')
                ->sum('amount');

            $opening = (float) $acc->opening_balance;

            if (in_array($acc->type, ['asset', 'expense'])) {
                $balance = $opening + $totalDebit - $totalCredit;
                $dr = $balance > 0 ? $balance : 0.0;
                $cr = $balance < 0 ? abs($balance) : 0.0;
            } else {
                $balance = $opening + $totalCredit - $totalDebit;
                $cr = $balance > 0 ? $balance : 0.0;
                $dr = $balance < 0 ? abs($balance) : 0.0;
            }

            if ($dr > 0 || $cr > 0 || $acc->opening_balance > 0) {
                $rows[] = [
                    'account' => $acc,
                    'debit' => $dr,
                    'credit' => $cr,
                ];
                $totalDr += $dr;
                $totalCr += $cr;
            }
        }

        return [
            'as_of_date' => $asOfDate,
            'rows' => $rows,
            'total_debit' => $totalDr,
            'total_credit' => $totalCr,
            'is_balanced' => abs($totalDr - $totalCr) < 0.01,
        ];
    }

    /**
     * Day Book / Cash Book for a specific date
     */
    public function getDayBook(string $date): array
    {
        $vouchers = AccountVoucher::with(['debitAccount', 'creditAccount', 'customer', 'supplier', 'employee'])
            ->whereDate('voucher_date', $date)
            ->orderBy('id', 'asc')
            ->get();

        $liquidAccounts = Account::where('type', 'asset')
            ->whereIn('category', ['Cash in Hand', 'Bank Account', 'Mobile Banking'])
            ->get();

        $totalIncomeToday = $vouchers->where('voucher_type', 'income')->sum('amount');
        $totalExpenseToday = $vouchers->where('voucher_type', 'expense')->sum('amount');
        $totalTransferToday = $vouchers->where('voucher_type', 'transfer')->sum('amount');

        return [
            'date' => $date,
            'vouchers' => $vouchers,
            'total_income' => $totalIncomeToday,
            'total_expense' => $totalExpenseToday,
            'total_transfer' => $totalTransferToday,
            'liquid_accounts' => $liquidAccounts,
        ];
    }

    /**
     * Resolve liquid payment account from method string
     */
    public function resolveLiquidAccount(?string $method = 'cash'): Account
    {
        // User rule: Full system e sob cash theke jabe asbe (Always strictly Main Cash 1001)
        return Account::where('account_code', '1001')->first() 
            ?? Account::where('category', 'Cash in Hand')->first() 
            ?? Account::where('type', 'asset')->first();
    }

    /**
     * ==========================================
     * FACTORY ERP MODULE INTEGRATIONS
     * ==========================================
     */

    /**
     * Sync Order Payment from Factory ERP Orders Module
     */
    public function syncOrderPayment(\App\Models\Order $order, string $paymentMethod = 'cash'): ?AccountVoucher
    {
        $paid = (float) $order->paid_amount;
        $order->loadMissing('customer');
        $ref = $order->order_number;

        $existing = AccountVoucher::where('reference_no', $ref)
            ->where('voucher_type', 'income')
            ->first();

        if ($paid <= 0) {
            if ($existing) {
                $this->deleteVoucher($existing);
            }
            return null;
        }

        $incomeAcc = Account::where('account_code', '4001')->first() 
            ?? Account::where('type', 'income')->first();
        $depositAcc = $this->resolveLiquidAccount($paymentMethod);

        if (!$incomeAcc || !$depositAcc) {
            return null;
        }

        $orderDate = Carbon::parse($order->order_date)->format('Y-m-d');

        if ($existing) {
            // Update amount if changed
            if ((float)$existing->amount !== $paid) {
                $this->deleteVoucher($existing);
                return $this->recordIncome([
                    'voucher_no' => $existing->voucher_no,
                    'voucher_date' => $orderDate,
                    'income_account_id' => $incomeAcc->id,
                    'deposit_account_id' => $depositAcc->id,
                    'amount' => $paid,
                    'party_name' => $order->customer?->company_name ?? 'Factory Order Customer',
                    'customer_id' => $order->customer_id,
                    'payment_method' => strtolower($paymentMethod),
                    'reference_no' => $ref,
                    'notes' => "Factory Order Sales Payment #{$order->order_number} (Total: ৳" . number_format($order->total_amount, 2) . ")",
                ]);
            }
            return $existing;
        }

        return $this->recordIncome([
            'voucher_date' => $orderDate,
            'income_account_id' => $incomeAcc->id,
            'deposit_account_id' => $depositAcc->id,
            'amount' => $paid,
            'party_name' => $order->customer?->company_name ?? 'Factory Order Customer',
            'customer_id' => $order->customer_id,
            'payment_method' => strtolower($paymentMethod),
            'reference_no' => $ref,
            'notes' => "Factory Order Sales Payment #{$order->order_number} (Total: ৳" . number_format($order->total_amount, 2) . ")",
        ]);
    }

    /**
     * Sync Customer Due Payment from Factory ERP Customer Due Module
     */
    public function syncCustomerDuePayment(\App\Models\Customer $customer, float $amount, string $paymentDate, string $method, string $ref, string $notes): ?AccountVoucher
    {
        if ($amount <= 0) return null;

        $incomeAcc = Account::where('account_code', '4001')->first() 
            ?? Account::where('type', 'income')->first();
        $depositAcc = $this->resolveLiquidAccount($method);

        if (!$incomeAcc || !$depositAcc) return null;

        return $this->recordIncome([
            'voucher_date' => $paymentDate,
            'income_account_id' => $incomeAcc->id,
            'deposit_account_id' => $depositAcc->id,
            'amount' => $amount,
            'party_name' => $customer->company_name,
            'customer_id' => $customer->id,
            'payment_method' => strtolower($method),
            'reference_no' => $ref,
            'notes' => "Customer Due Collection: {$customer->company_name} - {$notes}",
        ]);
    }

    /**
     * Sync Payroll Payment from Factory ERP HR Payroll Module
     */
    public function syncPayrollPayment(\App\Models\HrPayroll $payroll): ?AccountVoucher
    {
        $payroll->loadMissing(['employee']);
        $ref = "PAY-{$payroll->payroll_code}";

        $existing = AccountVoucher::where('reference_no', $ref)->first();

        if ($payroll->payment_status !== 'paid') {
            if ($existing) {
                app(VoucherReversalService::class)->reverse($existing, "Payroll marked unpaid");
            }
            return null;
        }

        $netSalary = (float) $payroll->net_salary;
        $advDeduction = (float) $payroll->advance_deduction;
        $grossSalary = round($netSalary + (float)$payroll->deductions, 2);

        if ($netSalary <= 0 && $grossSalary <= 0) return null;

        $salaryExpenseAcc = \App\Models\AccountingMapping::getAccount('payroll_expense', '5003');
        $salaryPayableAcc = \App\Models\AccountingMapping::getAccount('payroll_payable', '2010');
        $advanceReceivableAcc = \App\Models\AccountingMapping::getAccount('advance_receivable', '1060');
        $paidFromAcc = $this->resolveLiquidAccount($payroll->payment_method);

        if (!$salaryExpenseAcc || !$salaryPayableAcc || !$paidFromAcc) return null;

        $paymentDate = $payroll->payment_date ? Carbon::parse($payroll->payment_date)->format('Y-m-d') : date('Y-m-d');
        $empName = $payroll->employee ? "{$payroll->employee->name} ({$payroll->employee->employee_code})" : 'Worker';

        if ($existing) {
            return $existing;
        }

        // Post complete multi-line compound journal for Payroll Accrual, Advance Recovery, and Net Disbursement
        // Debit: Salary Expense (gross)
        // Credit: Employee Advance Receivable (advance deducted)
        // Credit: Cash/Bank (net paid)
        // If other deductions exist, credit appropriate clearing/payable
        $lines = [];
        $lines[] = [
            'account_id' => $salaryExpenseAcc->id,
            'type' => 'debit',
            'amount' => $netSalary + $advDeduction,
            'narration' => "Salary & wages for {$payroll->month} - {$empName}",
        ];

        if ($advDeduction > 0 && $advanceReceivableAcc) {
            $lines[] = [
                'account_id' => $advanceReceivableAcc->id,
                'type' => 'credit',
                'amount' => $advDeduction,
                'narration' => "Advance salary deduction recovery for {$empName}",
            ];
        }

        if ($netSalary > 0) {
            $lines[] = [
                'account_id' => $paidFromAcc->id,
                'type' => 'credit',
                'amount' => $netSalary,
                'narration' => "Net salary disbursement via " . ucfirst($payroll->payment_method ?: 'cash'),
            ];
        }

        return app(JournalPostingService::class)->post(
            [
                'voucher_type' => 'expense',
                'voucher_date' => $paymentDate,
                'party_name' => $empName,
                'employee_id' => $payroll->employee_id,
                'payment_method' => strtolower($payroll->payment_method ?: 'cash'),
                'reference_no' => $ref,
                'notes' => "Worker Payroll disbursement for {$payroll->month} - {$empName}",
            ],
            $lines,
            [
                'source_type' => \App\Models\HrPayroll::class,
                'source_id' => $payroll->id,
                'event_type' => 'payroll_disbursement',
            ]
        );
    }

    /**
     * Sync Advance Salary Payment from Factory ERP HR Advance Module
     */
    public function syncAdvancePayment(\App\Models\HrAdvance $advance): ?AccountVoucher
    {
        $advance->loadMissing(['employee']);
        $ref = "ADV-{$advance->id}";

        $existing = AccountVoucher::where('reference_no', $ref)->first();

        if ($advance->status !== 'approved' && $advance->status !== 'recovered') {
            if ($existing) {
                app(VoucherReversalService::class)->reverse($existing, "Advance rejected or cancelled");
            }
            return null;
        }

        $amount = (float) $advance->amount;
        if ($amount <= 0) return null;

        $advanceReceivableAcc = \App\Models\AccountingMapping::getAccount('advance_receivable', '1060');
        $paidFromAcc = $this->resolveLiquidAccount($advance->payment_method);

        if (!$advanceReceivableAcc || !$paidFromAcc) return null;

        $date = Carbon::parse($advance->advance_date)->format('Y-m-d');
        $empName = $advance->employee ? "{$advance->employee->name} ({$advance->employee->employee_code})" : 'Employee';

        if ($existing) {
            return $existing;
        }

        return app(JournalPostingService::class)->post(
            [
                'voucher_type' => 'expense',
                'voucher_date' => $date,
                'party_name' => $empName,
                'employee_id' => $advance->employee_id,
                'payment_method' => strtolower($advance->payment_method ?: 'cash'),
                'reference_no' => $ref,
                'notes' => "Advance Salary Loan disbursement for {$empName} (Deduct Month: {$advance->deduct_month})",
            ],
            [
                ['account_id' => $advanceReceivableAcc->id, 'type' => 'debit', 'amount' => $amount, 'narration' => "Advance salary loan to {$empName}"],
                ['account_id' => $paidFromAcc->id, 'type' => 'credit', 'amount' => $amount, 'narration' => "Advance salary disbursed via " . ucfirst($advance->payment_method ?: 'cash')],
            ],
            [
                'source_type' => \App\Models\HrAdvance::class,
                'source_id' => $advance->id,
                'event_type' => 'advance_disbursement',
            ]
        );
    }

    /**
     * Sync Product Delivery Sales Revenue into Accounts as Income Voucher
     */
    public function syncDeliveryRevenue(\App\Models\Delivery $delivery): ?AccountVoucher
    {
        $totalDeliverAmount = (float) $delivery->total_delivered_amount;
        if ($totalDeliverAmount <= 0) {
            return null;
        }

        $delivery->loadMissing(['customer', 'order']);
        $delNumber = $delivery->delivery_number;
        $orderNumber = $delivery->order?->order_number ?? 'N/A';
        $customerName = $delivery->customer?->company_name ?? 'Customer';

        $arAcc = \App\Models\AccountingMapping::getAccount('accounts_receivable', '1050');
        $salesAcc = \App\Models\AccountingMapping::getAccount('sales', '4001');

        if (!$arAcc || !$salesAcc) {
            return null;
        }

        $deliveryDate = $delivery->delivery_date ? Carbon::parse($delivery->delivery_date)->format('Y-m-d') : date('Y-m-d');

        return app(JournalPostingService::class)->post(
            [
                'voucher_type' => 'income',
                'voucher_date' => $deliveryDate,
                'party_name' => $customerName,
                'customer_id' => $delivery->customer_id,
                'payment_method' => 'other',
                'notes' => "Product Delivery Sales Revenue #{$delNumber} (Order #{$orderNumber})",
                'reference_no' => $delNumber,
            ],
            [
                ['account_id' => $arAcc->id, 'type' => 'debit', 'amount' => $totalDeliverAmount, 'narration' => "Accounts receivable for Delivery #{$delNumber}"],
                ['account_id' => $salesAcc->id, 'type' => 'credit', 'amount' => $totalDeliverAmount, 'narration' => "Product sales revenue for Delivery #{$delNumber}"],
            ],
            [
                'source_type' => \App\Models\Delivery::class,
                'source_id' => $delivery->id,
                'event_type' => 'sales_revenue',
            ]
        );
    }

    /**
     * Master Sync: Sync all existing Factory ERP data into Accounts
     */
    public function syncAllFactoryData(): array
    {
        $ordersCount = 0;
        $payrollsCount = 0;
        $advancesCount = 0;
        $deliveriesCount = 0;

        // 1. Sync all product delivery sales revenue
        $deliveries = \App\Models\Delivery::where('total_delivered_amount', '>', 0)->get();
        foreach ($deliveries as $delivery) {
            if ($this->syncDeliveryRevenue($delivery)) {
                $deliveriesCount++;
            }
        }

        // 2. Sync all paid orders
        $orders = \App\Models\Order::where('paid_amount', '>', 0)->get();
        foreach ($orders as $order) {
            if ($this->syncOrderPayment($order)) {
                $ordersCount++;
            }
        }

        // 3. Sync all paid payrolls
        $payrolls = \App\Models\HrPayroll::where('payment_status', 'paid')->get();
        foreach ($payrolls as $payroll) {
            if ($this->syncPayrollPayment($payroll)) {
                $payrollsCount++;
            }
        }

        // 4. Sync all approved advances
        $advances = \App\Models\HrAdvance::whereIn('status', ['approved', 'recovered'])->get();
        foreach ($advances as $advance) {
            if ($this->syncAdvancePayment($advance)) {
                $advancesCount++;
            }
        }

        return [
            'deliveries_synced' => $deliveriesCount,
            'orders_synced' => $ordersCount,
            'payrolls_synced' => $payrollsCount,
            'advances_synced' => $advancesCount,
            'total_synced' => $deliveriesCount + $ordersCount + $payrollsCount + $advancesCount,
        ];
    }
}

