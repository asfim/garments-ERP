<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryCostLayer extends Model
{
    protected $fillable = [
        'product_id',
        'production_id',
        'batch_number',
        'received_date',
        'initial_qty',
        'remaining_qty',
        'unit_cost',
        'total_cost',
        'status', // active, exhausted, adjusted
        'notes',
    ];

    protected $casts = [
        'received_date' => 'date',
        'initial_qty' => 'decimal:2',
        'remaining_qty' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function production(): BelongsTo
    {
        return $this->belongsTo(Production::class, 'production_id');
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(InventoryCostAllocation::class, 'inventory_cost_layer_id');
    }

    public function adjustments(): HasMany
    {
        return $this->hasMany(InventoryCostAdjustment::class, 'inventory_cost_layer_id');
    }

    public function isFullyConsumed(): bool
    {
        return (float) $this->remaining_qty <= 0.0001;
    }

    public function isUntouched(): bool
    {
        return abs((float)$this->remaining_qty - (float)$this->initial_qty) < 0.0001;
    }
}

