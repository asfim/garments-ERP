<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryCostAdjustment extends Model
{
    protected $fillable = [
        'inventory_cost_layer_id',
        'adjustment_type',
        'qty_change',
        'cost_impact',
        'reason',
        'adjusted_by',
    ];

    protected $casts = [
        'qty_change' => 'decimal:2',
        'cost_impact' => 'decimal:2',
    ];

    public function costLayer(): BelongsTo
    {
        return $this->belongsTo(InventoryCostLayer::class, 'inventory_cost_layer_id');
    }

    public function adjustedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'adjusted_by');
    }
}

