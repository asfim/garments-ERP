<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountingMapping extends Model
{
    protected $fillable = [
        'mapping_key',
        'account_id',
        'title',
        'description',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    /**
     * Resolve account id by mapping key, with fallback to account code or default
     */
    public static function getAccount(string $mappingKey, ?string $fallbackCode = null): ?Account
    {
        $mapping = self::with('account')->where('mapping_key', $mappingKey)->first();
        if ($mapping && $mapping->account && $mapping->account->status === 'active') {
            return $mapping->account;
        }

        if ($fallbackCode) {
            $acc = Account::where('account_code', $fallbackCode)->where('status', 'active')->first();
            if ($acc) return $acc;
        }

        return null;
    }
}

