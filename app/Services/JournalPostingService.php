<?php

namespace App\Services;

use App\Models\Account;
use App\Models\AccountVoucher;
use App\Models\AccountVoucherLine;
use App\Models\AccountTransaction;
use App\Models\AccountingSourceLink;
use App\Models\AccountingAuditLog;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class JournalPostingService
{
    /**
     * Convenient wrapper accepting a combined array with lines or debit_lines/credit_lines
     */
    public function postJournal(array $data, ?array $source = null): AccountVoucher
    {
        $lines = [];
        if (isset($data['lines']) && is_array($data['lines'])) {
            $lines = $data['lines'];
        } else {
            if (!empty($data['debit_lines'])) {
                foreach ($data['debit_lines'] as $dl) {
                    $lines[] = array_merge($dl, ['type' => 'debit']);
                }
            }
            if (!empty($data['credit_lines'])) {
                foreach ($data['credit_lines'] as $cl) {
                    $lines[] = array_merge($cl, ['type' => 'credit']);
                }
            }
        }

        unset($data['lines'], $data['debit_lines'], $data['credit_lines']);
        return $this->post($data, $lines, $source);
    }

    /**
     * Post a multi-line balanced double-entry journal voucher
     *
     * @param array $voucherData [voucher_type, voucher_date, notes, payment_method, reference_no, party_name, customer_id, supplier_id, employee_id, attachment]
     * @param array $lines Array of ['account_id' => int, 'type' => 'debit'|'credit', 'amount' => float, 'narration' => ?string]
     * @param array|null $source [source_type => string, source_id => int, event_type => string]
     * @return AccountVoucher
     */
    public function post(array $voucherData, array $lines, ?array $source = null): AccountVoucher
    {
        // 1. Basic validation
        if (empty($lines) || count($lines) < 2) {
            throw new InvalidArgumentException("A double-entry journal requires at least two lines (one debit and one credit).");
        }

        // 2. Check for duplicate posting via source link
        if ($source && !empty($source['source_type']) && !empty($source['source_id']) && !empty($source['event_type'])) {
            $existingLink = AccountingSourceLink::with('voucher')
                ->where('source_type', $source['source_type'])
                ->where('source_id', $source['source_id'])
                ->where('event_type', $source['event_type'])
                ->first();

            if ($existingLink && $existingLink->voucher) {
                // Already posted. Return existing voucher to guarantee idempotency.
                return $existingLink->voucher;
            }
        }

        // 3. Mathematical validation of debits and credits
        $totalDebit = 0.0;
        $totalCredit = 0.0;
        $accountIds = [];

        foreach ($lines as $line) {
            $amt = round((float) ($line['amount'] ?? 0), 2);
            if ($amt <= 0) {
                throw new InvalidArgumentException("Every journal line amount must be greater than zero.");
            }

            $type = strtolower($line['type'] ?? '');
            if (!in_array($type, ['debit', 'credit'])) {
                throw new InvalidArgumentException("Invalid line type '{$type}'. Must be 'debit' or 'credit'.");
            }

            if ($type === 'debit') {
                $totalDebit = round($totalDebit + $amt, 2);
            } else {
                $totalCredit = round($totalCredit + $amt, 2);
            }

            $accId = (int) ($line['account_id'] ?? 0);
            if ($accId <= 0) {
                throw new InvalidArgumentException("Account ID must be specified for each journal line.");
            }
            $accountIds[] = $accId;
        }

        if (abs($totalDebit - $totalCredit) > 0.001) {
            throw new InvalidArgumentException(
                sprintf("Unbalanced Journal! Total Debit (৳%.2f) must equal Total Credit (৳%.2f). Difference: ৳%.2f",
                    $totalDebit, $totalCredit, abs($totalDebit - $totalCredit)
                )
            );
        }

        $accountIds = array_unique($accountIds);

        // 4. Execute atomic database transaction with pessimistic row locks
        return DB::transaction(function () use ($voucherData, $lines, $source, $totalDebit, $accountIds) {
            // Lock and verify all participating accounts
            $accounts = Account::whereIn('id', $accountIds)->lockForUpdate()->get()->keyBy('id');

            foreach ($accountIds as $accId) {
                if (!isset($accounts[$accId])) {
                    throw new RuntimeException("Account with ID {$accId} does not exist.");
                }
                if ($accounts[$accId]->status !== 'active') {
                    throw new RuntimeException("Account [{$accounts[$accId]->account_code}] {$accounts[$accId]->name} is inactive and cannot accept postings.");
                }
            }

            // Identify primary debit and credit accounts for backward-compatible columns
            $firstDebitId = null;
            $firstCreditId = null;
            foreach ($lines as $line) {
                if ($line['type'] === 'debit' && !$firstDebitId) $firstDebitId = $line['account_id'];
                if ($line['type'] === 'credit' && !$firstCreditId) $firstCreditId = $line['account_id'];
            }

            $voucherType = $voucherData['voucher_type'] ?? 'journal';
            $voucherNo = $voucherData['voucher_no'] ?? AccountVoucher::generateVoucherNo($voucherType);
            $voucherDate = $voucherData['voucher_date'] ?? date('Y-m-d');

            // Create Voucher Header
            $voucher = AccountVoucher::create([
                'voucher_no' => $voucherNo,
                'voucher_type' => $voucherType,
                'status' => 'posted',
                'is_reversed' => false,
                'source_module' => $source['source_type'] ?? null,
                'source_id' => $source['source_id'] ?? null,
                'voucher_date' => $voucherDate,
                'amount' => $totalDebit,
                'debit_account_id' => $firstDebitId,
                'credit_account_id' => $firstCreditId,
                'party_name' => $voucherData['party_name'] ?? null,
                'customer_id' => $voucherData['customer_id'] ?? null,
                'supplier_id' => $voucherData['supplier_id'] ?? null,
                'employee_id' => $voucherData['employee_id'] ?? null,
                'payment_method' => $voucherData['payment_method'] ?? 'cash',
                'reference_no' => $voucherData['reference_no'] ?? null,
                'cheque_date' => $voucherData['cheque_date'] ?? null,
                'attachment' => $voucherData['attachment'] ?? null,
                'notes' => $voucherData['notes'] ?? null,
                'created_by' => auth()->id() ?? null,
            ]);

            // Create Multi-line records and General Ledger transactions
            foreach ($lines as $line) {
                $amt = round((float) $line['amount'], 2);
                $narration = $line['narration'] ?? ($voucherData['notes'] ?? ucfirst($line['type']) . ' entry');

                // 1. Voucher line
                AccountVoucherLine::create([
                    'voucher_id' => $voucher->id,
                    'account_id' => $line['account_id'],
                    'type' => $line['type'],
                    'amount' => $amt,
                    'narration' => $narration,
                ]);

                // 2. Ledger transaction row
                AccountTransaction::create([
                    'voucher_id' => $voucher->id,
                    'account_id' => $line['account_id'],
                    'transaction_date' => $voucherDate,
                    'type' => $line['type'],
                    'amount' => $amt,
                    'narration' => $narration,
                    'reference_no' => $voucherData['reference_no'] ?? null,
                ]);
            }

            // Create Source Link if applicable
            if ($source && !empty($source['source_type']) && !empty($source['source_id']) && !empty($source['event_type'])) {
                AccountingSourceLink::create([
                    'source_type' => $source['source_type'],
                    'source_id' => $source['source_id'],
                    'event_type' => $source['event_type'],
                    'voucher_id' => $voucher->id,
                ]);
            }

            // Audit log
            AccountingAuditLog::log(
                'posted',
                $voucher->id,
                "Posted journal voucher {$voucher->voucher_no} with total ৳" . number_format($totalDebit, 2)
            );

            // Recalculate balances for all affected accounts inside the transaction
            foreach ($accountIds as $accId) {
                $accounts[$accId]->recalculateBalance();
            }

            return $voucher;
        });
    }
}
