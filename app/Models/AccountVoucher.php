<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountVoucher extends Model
{
    protected $fillable = [
        'voucher_no',
        'voucher_type', // income, expense, transfer, journal
        'status', // draft, posted, reversed
        'is_reversed',
        'reversed_by_voucher_id',
        'reversal_reason',
        'source_module',
        'source_id',
        'voucher_date',
        'amount',
        'debit_account_id',
        'credit_account_id',
        'party_name',
        'customer_id',
        'supplier_id',
        'employee_id',
        'payment_method',
        'reference_no',
        'cheque_date',
        'attachment',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'voucher_date' => 'date',
        'cheque_date' => 'date',
        'amount' => 'decimal:2',
        'is_reversed' => 'boolean',
    ];

    public function debitAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'debit_account_id');
    }

    public function creditAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'credit_account_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(HrEmployee::class, 'employee_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(AccountTransaction::class, 'voucher_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(AccountVoucherLine::class, 'voucher_id');
    }

    public function sourceLink()
    {
        return $this->hasOne(AccountingSourceLink::class, 'voucher_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AccountingAuditLog::class, 'voucher_id');
    }

    public function reversal()
    {
        return $this->hasOne(AccountingReversal::class, 'original_voucher_id');
    }

    public function reversedByVoucher(): BelongsTo
    {
        return $this->belongsTo(AccountVoucher::class, 'reversed_by_voucher_id');
    }

    /**
     * Generate automatic next voucher number by type
     */
    public static function generateVoucherNo(string $type): string
    {
        $prefix = match ($type) {
            'income' => 'INC',
            'expense' => 'EXP',
            'transfer' => 'TRF',
            'journal' => 'JRN',
            default => 'VCH',
        };

        $year = date('Y');
        $last = self::where('voucher_type', $type)
            ->whereYear('voucher_date', $year)
            ->orderBy('id', 'desc')
            ->first();

        $seq = 1;
        if ($last && preg_match('/-(\d+)$/', $last->voucher_no, $m)) {
            $seq = (int)$m[1] + 1;
        }

        return sprintf('%s-%s-%04d', $prefix, $year, $seq);
    }

    /**
     * Human-friendly payment method label
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'cash' => 'Cash (নগদ)',
            'bank', 'bank_transfer' => 'Bank Transfer (ব্যাংক)',
            'cheque' => 'Cheque (চেক)',
            'bkash' => 'bKash (বিকাশ)',
            'nagad' => 'Nagad (নগদ)',
            'rocket' => 'Rocket (রকেট)',
            default => ucfirst($this->payment_method ?? 'Cash'),
        };
    }
}

