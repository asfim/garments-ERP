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
}

