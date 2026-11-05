<?php

namespace App\Services;

use App\Models\Account;
use App\Models\AccountVoucher;
use App\Models\AccountTransaction;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\HrEmployee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialReportingService
{
    /**
     * Profit & Loss Statement using posted journal lines as the single source of truth
     */
    public function getProfitAndLoss(?string $fromDate = null, ?string $toDate = null): array
    {
        $fromDate = $fromDate ?: Carbon::now()->startOfMonth()->format('Y-m-d');
        $toDate = $toDate ?: Carbon::now()->format('Y-m-d');

        // 1. Gross Sales Revenue & Other Income
        $incomeAccounts = Account::where('type', 'income')->get();
        $incomeDetails = [];
        $grossSales = 0.0;
        $otherIncome = 0.0;

        foreach ($incomeAccounts as $acc) {
            $credits = (float) $acc->transactions()
                ->whereHas('voucher', fn($q) => $q->where('status', 'posted'))
                ->whereBetween('transaction_date', [$fromDate, $toDate])
                ->where('type', 'credit')
                ->sum('amount');

            $debits = (float) $acc->transactions()
                ->whereHas('voucher', fn($q) => $q->where('status', 'posted'))
                ->whereBetween('transaction_date', [$fromDate, $toDate])
                ->where('type', 'debit')
                ->sum('amount');

            $net = round($credits - $debits, 2);
            if ($net != 0 || $credits > 0) {
                $incomeDetails[] = [
                    'account' => $acc,
                    'amount' => $net,
                ];
                if ($acc->account_code === '4001' || str_contains(strtolower($acc->name), 'sales')) {
                    $grossSales = round($grossSales + $net, 2);
                } else {
                    $otherIncome = round($otherIncome + $net, 2);
                }
            }
        }
        $totalRevenue = round($grossSales + $otherIncome, 2);

        // 2. FIFO Cost of Goods Sold (Account 5100)
        $cogsAccount = Account::where('account_code', '5100')->first();
        $totalCogs = 0.0;
        if ($cogsAccount) {
            $cogsDr = (float) $cogsAccount->transactions()
                ->whereHas('voucher', fn($q) => $q->where('status', 'posted'))
                ->whereBetween('transaction_date', [$fromDate, $toDate])
                ->where('type', 'debit')
                ->sum('amount');
            $cogsCr = (float) $cogsAccount->transactions()
                ->whereHas('voucher', fn($q) => $q->where('status', 'posted'))
                ->whereBetween('transaction_date', [$fromDate, $toDate])
                ->where('type', 'credit')
                ->sum('amount');
            $totalCogs = max(0, round($cogsDr - $cogsCr, 2));
        }

        // Gross Profit = Total Revenue (or Gross Sales) - FIFO COGS
        $grossProfit = round($grossSales - $totalCogs, 2);
        $grossMargin = $grossSales > 0 ? round(($grossProfit / $grossSales) * 100, 2) : 0.0;

        // 3. Operating & Administrative Expenses (Excluding COGS & Production Absorption)
        $expenseAccounts = Account::where('type', 'expense')
            ->whereNotIn('account_code', ['5100', '5200'])
            ->get();

        $expenseDetails = [];
        $totalOperatingExpenses = 0.0;

        foreach ($expenseAccounts as $acc) {
            $debits = (float) $acc->transactions()
                ->whereHas('voucher', fn($q) => $q->where('status', 'posted'))
                ->whereBetween('transaction_date', [$fromDate, $toDate])
                ->where('type', 'debit')
                ->sum('amount');

            $credits = (float) $acc->transactions()
                ->whereHas('voucher', fn($q) => $q->where('status', 'posted'))
                ->whereBetween('transaction_date', [$fromDate, $toDate])
                ->where('type', 'credit')
                ->sum('amount');

            $net = round($debits - $credits, 2);
            if ($net != 0 || $debits > 0) {
                $expenseDetails[] = [
                    'account' => $acc,
                    'amount' => $net,
                ];
                $totalOperatingExpenses = round($totalOperatingExpenses + $net, 2);
            }
        }

        // Net Operating Profit = Gross Profit + Other Income - Total Operating Expenses
        $netProfit = round($grossProfit + $otherIncome - $totalOperatingExpenses, 2);
        $netMargin = $totalRevenue > 0 ? round(($netProfit / $totalRevenue) * 100, 2) : 0.0;

        return [
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'gross_sales' => $grossSales,
            'other_income' => $otherIncome,
            'total_revenue' => $totalRevenue,
            'cogs' => $totalCogs,
            'gross_profit' => $grossProfit,
            'gross_margin' => $grossMargin,
            'operating_expenses' => $totalOperatingExpenses,
            'expense_details' => $expenseDetails,
            'income_details' => $incomeDetails,
            'net_profit' => $netProfit,
            'net_margin' => $netMargin,
        ];
    }

    /**
     * Trial Balance Statement (All Accounts: Total Debit = Total Credit)
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
                ->whereHas('voucher', fn($q) => $q->where('status', 'posted'))
                ->whereDate('transaction_date', '<=', $asOfDate)
                ->where('type', 'debit')
                ->sum('amount');

            $totalCredit = (float) $acc->transactions()
                ->whereHas('voucher', fn($q) => $q->where('status', 'posted'))
                ->whereDate('transaction_date', '<=', $asOfDate)
                ->where('type', 'credit')
                ->sum('amount');

            $opening = (float) $acc->opening_balance;

            if (in_array($acc->type, ['asset', 'expense'])) {
                $balance = round($opening + $totalDebit - $totalCredit, 2);
                $dr = $balance > 0 ? $balance : 0.0;
                $cr = $balance < 0 ? abs($balance) : 0.0;
            } else {
                $balance = round($opening + $totalCredit - $totalDebit, 2);
                $cr = $balance > 0 ? $balance : 0.0;
                $dr = $balance < 0 ? abs($balance) : 0.0;
            }

            if ($dr > 0 || $cr > 0 || $opening > 0) {
                $rows[] = [
                    'account' => $acc,
                    'debit' => $dr,
                    'credit' => $cr,
                ];
                $totalDr = round($totalDr + $dr, 2);
                $totalCr = round($totalCr + $cr, 2);
            }
        }

        return [
            'as_of_date' => $asOfDate,
            'rows' => $rows,
            'total_debit' => $totalDr,
            'total_credit' => $totalCr,
            'is_balanced' => abs($totalDr - $totalCr) < 0.01,
            'difference' => round(abs($totalDr - $totalCr), 2),
        ];
    }

    /**
     * Balance Sheet Statement (Assets = Liabilities + Equity)
     */
    public function getBalanceSheet(?string $asOfDate = null): array
    {
        $asOfDate = $asOfDate ?: Carbon::now()->format('Y-m-d');

        // Assets
        $assetAccounts = Account::where('type', 'asset')->orderBy('account_code')->get();
        $assetList = [];
        $totalAssets = 0.0;

        foreach ($assetAccounts as $acc) {
            $bal = $this->calculateAccountBalanceAsOf($acc, $asOfDate);
            if ($bal != 0 || $acc->opening_balance > 0) {
                $assetList[] = ['account' => $acc, 'balance' => $bal];
                $totalAssets = round($totalAssets + $bal, 2);
            }
        }

        // Liabilities
        $liabilityAccounts = Account::where('type', 'liability')->orderBy('account_code')->get();
        $liabilityList = [];
        $totalLiabilities = 0.0;

        foreach ($liabilityAccounts as $acc) {
            $bal = $this->calculateAccountBalanceAsOf($acc, $asOfDate);
            if ($bal != 0 || $acc->opening_balance > 0) {
                $liabilityList[] = ['account' => $acc, 'balance' => $bal];
                $totalLiabilities = round($totalLiabilities + $bal, 2);
            }
        }

        // Equity
        $equityAccounts = Account::where('type', 'equity')->orderBy('account_code')->get();
        $equityList = [];
        $totalEquity = 0.0;

        foreach ($equityAccounts as $acc) {
            $bal = $this->calculateAccountBalanceAsOf($acc, $asOfDate);
            if ($bal != 0 || $acc->opening_balance > 0) {
                $equityList[] = ['account' => $acc, 'balance' => $bal];
                $totalEquity = round($totalEquity + $bal, 2);
            }
        }

        // Current period retained earnings (cumulative net profit up to as of date)
        $pnl = $this->getProfitAndLoss('1970-01-01', $asOfDate);
        $periodEarnings = $pnl['net_profit'];
        $totalEquityAndEarnings = round($totalEquity + $periodEarnings, 2);
        $totalLiabilitiesAndEquity = round($totalLiabilities + $totalEquityAndEarnings, 2);

        return [
            'as_of_date' => $asOfDate,
            'assets' => $assetList,
            'total_assets' => $totalAssets,
            'liabilities' => $liabilityList,
            'total_liabilities' => $totalLiabilities,
            'equity' => $equityList,
            'total_equity' => $totalEquity,
            'period_earnings' => $periodEarnings,
            'total_liabilities_and_equity' => $totalLiabilitiesAndEquity,
            'is_balanced' => abs($totalAssets - $totalLiabilitiesAndEquity) < 0.01,
        ];
    }

    /**
     * Helper to compute an account's balance up to a date
     */
    protected function calculateAccountBalanceAsOf(Account $account, string $asOfDate): float
    {
        $debits = (float) $account->transactions()
            ->whereHas('voucher', fn($q) => $q->where('status', 'posted'))
            ->whereDate('transaction_date', '<=', $asOfDate)
            ->where('type', 'debit')
            ->sum('amount');

        $credits = (float) $account->transactions()
            ->whereHas('voucher', fn($q) => $q->where('status', 'posted'))
            ->whereDate('transaction_date', '<=', $asOfDate)
            ->where('type', 'credit')
            ->sum('amount');

        $opening = (float) $account->opening_balance;

        if (in_array($account->type, ['asset', 'expense'])) {
            return round($opening + $debits - $credits, 2);
        } else {
            return round($opening + $credits - $debits, 2);
        }
    }

    /**
     * Sub-ledger for a specific Customer
     */
    public function getCustomerLedger(Customer $customer, ?string $fromDate = null, ?string $toDate = null): array
    {
        $fromDate = $fromDate ?: Carbon::now()->startOfMonth()->format('Y-m-d');
        $toDate = $toDate ?: Carbon::now()->format('Y-m-d');

        // Customer transactions from AccountVouchers
        $vouchers = AccountVoucher::with(['lines.account', 'debitAccount', 'creditAccount'])
            ->where('customer_id', $customer->id)
            ->where('status', 'posted')
            ->whereBetween('voucher_date', [$fromDate, $toDate])
            ->orderBy('voucher_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $rows = [];
        $running = 0.0;

        foreach ($vouchers as $v) {
            // If sales shipment / invoice -> Increases receivable (Debit)
            // If payment collection -> Decreases receivable (Credit)
            $dr = 0.0;
            $cr = 0.0;

            if ($v->source_module === \App\Models\Delivery::class || $v->voucher_type === 'journal') {
                $dr = (float) $v->amount;
                $running = round($running + $dr, 2);
            } else {
                $cr = (float) $v->amount;
                $running = round($running - $cr, 2);
            }

            $rows[] = [
                'date' => $v->voucher_date->format('Y-m-d'),
                'voucher_no' => $v->voucher_no,
                'description' => $v->notes ?? 'Transaction',
                'payment_method' => $v->payment_method,
                'reference_no' => $v->reference_no,
                'debit' => $dr,
                'credit' => $cr,
                'running_balance' => $running,
            ];
        }

        return [
            'customer' => $customer,
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'rows' => $rows,
            'closing_balance' => $running,
        ];
    }

    /**
     * Sub-ledger for a specific Supplier
     */
    public function getSupplierLedger(Supplier $supplier, ?string $fromDate = null, ?string $toDate = null): array
    {
        $fromDate = $fromDate ?: Carbon::now()->startOfMonth()->format('Y-m-d');
        $toDate = $toDate ?: Carbon::now()->format('Y-m-d');

        $vouchers = AccountVoucher::with(['lines.account', 'debitAccount', 'creditAccount'])
            ->where('supplier_id', $supplier->id)
            ->where('status', 'posted')
            ->whereBetween('voucher_date', [$fromDate, $toDate])
            ->orderBy('voucher_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $rows = [];
        $running = 0.0;

        foreach ($vouchers as $v) {
            $dr = 0.0;
            $cr = 0.0;

            if ($v->voucher_type === 'expense' || $v->voucher_type === 'payment') {
                // Payment to supplier reduces payable (Debit)
                $dr = (float) $v->amount;
                $running = round($running - $dr, 2);
            } else {
                // Bill / Purchase from supplier increases payable (Credit)
                $cr = (float) $v->amount;
                $running = round($running + $cr, 2);
            }

            $rows[] = [
                'date' => $v->voucher_date->format('Y-m-d'),
                'voucher_no' => $v->voucher_no,
                'description' => $v->notes ?? 'Supplier Transaction',
                'payment_method' => $v->payment_method,
                'reference_no' => $v->reference_no,
                'debit' => $dr,
                'credit' => $cr,
                'running_balance' => $running,
            ];
        }

        return [
            'supplier' => $supplier,
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'rows' => $rows,
            'closing_balance' => $running,
        ];
    }

    /**
     * Sub-ledger for an Employee (Advances & Payroll)
     */
    public function getEmployeeLedger(HrEmployee $employee, ?string $fromDate = null, ?string $toDate = null): array
    {
        $fromDate = $fromDate ?: Carbon::now()->startOfMonth()->format('Y-m-d');
        $toDate = $toDate ?: Carbon::now()->format('Y-m-d');

        $vouchers = AccountVoucher::with(['lines.account', 'debitAccount', 'creditAccount'])
            ->where('employee_id', $employee->id)
            ->where('status', 'posted')
            ->whereBetween('voucher_date', [$fromDate, $toDate])
            ->orderBy('voucher_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $rows = [];
        $runningAdvance = 0.0;

        foreach ($vouchers as $v) {
            $advanceGiven = 0.0;
            $advanceRecovered = 0.0;

            if (str_contains(strtolower($v->notes ?? ''), 'advance disbursement') || $v->voucher_type === 'expense') {
                $advanceGiven = (float) $v->amount;
                $runningAdvance = round($runningAdvance + $advanceGiven, 2);
            } elseif (str_contains(strtolower($v->notes ?? ''), 'advance recovery') || $v->voucher_type === 'journal') {
                // Look for credit line to advance receivable
                $recLine = $v->lines->first(fn($l) => $l->account?->account_code === '1060' && $l->type === 'credit');
                $recovered = $recLine ? (float)$recLine->amount : 0.0;
                $advanceRecovered = $recovered;
                $runningAdvance = round($runningAdvance - $advanceRecovered, 2);
            }

            $rows[] = [
                'date' => $v->voucher_date->format('Y-m-d'),
                'voucher_no' => $v->voucher_no,
                'description' => $v->notes ?? 'Employee Transaction',
                'payment_method' => $v->payment_method,
                'reference_no' => $v->reference_no,
                'amount' => (float) $v->amount,
                'advance_given' => $advanceGiven,
                'advance_recovered' => $advanceRecovered,
                'running_advance_balance' => $runningAdvance,
            ];
        }

        return [
            'employee' => $employee,
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'rows' => $rows,
            'closing_advance_balance' => $runningAdvance,
        ];
    }
}
