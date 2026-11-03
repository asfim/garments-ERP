<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    protected $fillable = [
        'account_code',
        'name',
        'type', // asset, liability, equity, income, expense
        'category',
        'bank_name',
        'account_number',
        'branch_name',
        'routing_number',
        'opening_balance',
        'current_balance',
        'is_system',
        'status',
        'description',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_system' => 'boolean',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(AccountTransaction::class, 'account_id');
    }

    public function debitVouchers(): HasMany
    {
        return $this->hasMany(AccountVoucher::class, 'debit_account_id');
    }

    public function creditVouchers(): HasMany
    {
        return $this->hasMany(AccountVoucher::class, 'credit_account_id');
    }

    /**
     * Check if account is a liquid bank/cash account
     */
    public function isLiquid(): bool
    {
        return $this->type === 'asset' && in_array($this->category, ['Cash in Hand', 'Bank Account', 'Mobile Banking']);
    }

    /**
     * Recalculate and update current balance from ledger transactions
     */
    public function recalculateBalance(): float
    {
        $totalDebit = (float) $this->transactions()->where('type', 'debit')->sum('amount');
        $totalCredit = (float) $this->transactions()->where('type', 'credit')->sum('amount');
        $opening = (float) $this->opening_balance;

        // Assets and Expenses: normal balance = Debit (+Debit, -Credit)
        // Liabilities, Equity, Income: normal balance = Credit (+Credit, -Debit)
        if (in_array($this->type, ['asset', 'expense'])) {
            $balance = $opening + $totalDebit - $totalCredit;
        } else {
            $balance = $opening + $totalCredit - $totalDebit;
        }

        $this->current_balance = $balance;
        $this->save();

        return $balance;
    }

    /**
     * Badge CSS class for account type
     */
    public function getTypeBadgeClassAttribute(): string
    {
        return match ($this->type) {
            'asset' => 'bg-cyan-500/10 text-cyan-400 border-cyan-500/20',
            'liability' => 'bg-rose-500/10 text-rose-400 border-rose-500/20',
            'equity' => 'bg-purple-500/10 text-purple-400 border-purple-500/20',
            'income' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
            'expense' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
            default => 'bg-slate-500/10 text-slate-400 border-slate-500/20',
        };
    }
}

