<?php

namespace App\Services;

use App\Models\Account;
use App\Models\AccountVoucher;
use App\Models\AccountingReversal;
use App\Models\AccountingAuditLog;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class VoucherReversalService
{
    protected JournalPostingService $journalService;

    public function __construct(JournalPostingService $journalService)
    {
        $this->journalService = $journalService;
    }

    /**
     * Alias for reverse()
     */
    public function reverseVoucher(AccountVoucher $voucher, string $reason): AccountVoucher
    {
        return $this->reverse($voucher, $reason);
    }

    /**
     * Reverse an existing posted voucher by creating an exact contra-reversal voucher
     *
     * @param AccountVoucher $voucher
     * @param string $reason
     * @return AccountVoucher The newly created reversal voucher
     */
    public function reverse(AccountVoucher $voucher, string $reason): AccountVoucher
    {
        if ($voucher->is_reversed || $voucher->status === 'reversed') {
            throw new InvalidArgumentException("Voucher {$voucher->voucher_no} is already reversed.");
        }

        if ($voucher->status === 'draft') {
            throw new InvalidArgumentException("Draft vouchers cannot be reversed; they can be deleted directly.");
        }

        $voucher->loadMissing(['lines', 'transactions']);

        // Check lines or transactions
        $lines = $voucher->lines;
        $reversalLines = [];

        if ($lines->isNotEmpty()) {
            foreach ($lines as $line) {
                $oppositeType = $line->type === 'debit' ? 'credit' : 'debit';
                $reversalLines[] = [
                    'account_id' => $line->account_id,
                    'type' => $oppositeType,
                    'amount' => (float) $line->amount,
                    'narration' => "Reversal of {$voucher->voucher_no}: " . ($line->narration ?: $reason),
                ];
            }
        } elseif ($voucher->transactions->isNotEmpty()) {
            foreach ($voucher->transactions as $tx) {
                $oppositeType = $tx->type === 'debit' ? 'credit' : 'debit';
                $reversalLines[] = [
                    'account_id' => $tx->account_id,
                    'type' => $oppositeType,
                    'amount' => (float) $tx->amount,
                    'narration' => "Reversal of {$voucher->voucher_no}: " . ($tx->narration ?: $reason),
                ];
            }
        } else {
            throw new RuntimeException("Voucher {$voucher->voucher_no} contains no transaction lines to reverse.");
        }

        return DB::transaction(function () use ($voucher, $reason, $reversalLines) {
            $revVoucherData = [
                'voucher_type' => 'journal',
                'voucher_date' => date('Y-m-d'),
                'notes' => "Reversal voucher for {$voucher->voucher_no}. Reason: {$reason}",
                'reference_no' => "REV-{$voucher->voucher_no}",
                'party_name' => $voucher->party_name,
                'customer_id' => $voucher->customer_id,
                'supplier_id' => $voucher->supplier_id,
                'employee_id' => $voucher->employee_id,
                'payment_method' => $voucher->payment_method,
            ];

            // Post reversal voucher via JournalPostingService
            $reversalVoucher = $this->journalService->post($revVoucherData, $reversalLines);

            // Mark original voucher as reversed
            $voucher->status = 'reversed';
            $voucher->is_reversed = true;
            $voucher->reversed_by_voucher_id = $reversalVoucher->id;
            $voucher->reversal_reason = $reason;
            $voucher->save();

            // Record Reversal Pair
            AccountingReversal::create([
                'original_voucher_id' => $voucher->id,
                'reversal_voucher_id' => $reversalVoucher->id,
                'reason' => $reason,
                'reversed_by' => auth()->id() ?? null,
                'reversed_at' => now(),
            ]);

            // Audit Log
            AccountingAuditLog::log(
                'reversed',
                $voucher->id,
                "Voucher {$voucher->voucher_no} reversed by {$reversalVoucher->voucher_no}. Reason: {$reason}"
            );

            return $reversalVoucher;
        });
    }
}
