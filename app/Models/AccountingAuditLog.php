<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountingAuditLog extends Model
{
    protected $fillable = [
        'voucher_id',
        'action', // posted, reversed, draft_created, balance_recalculated
        'user_id',
        'details',
        'ip_address',
    ];

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(AccountVoucher::class, 'voucher_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function log(string $action, ?int $voucherId = null, ?string $details = null): self
    {
        return self::create([
            'voucher_id' => $voucherId,
            'action' => $action,
            'user_id' => auth()->id() ?? null,
            'details' => $details,
            'ip_address' => request()->ip() ?? null,
        ]);
    }
}

