<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountingSourceLink extends Model
{
    protected $fillable = [
        'source_type',
        'source_id',
        'event_type',
        'voucher_id',
    ];

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(AccountVoucher::class, 'voucher_id');
    }

    /**
     * Check if a specific source event is already posted to accounts
     */
    public static function hasPosted(string $sourceType, int|string $sourceId, string $eventType): bool
    {
        return self::where('source_type', $sourceType)
            ->where('source_id', $sourceId)
            ->where('event_type', $eventType)
            ->exists();
    }
}

