<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryCostAllocation extends Model
{
    protected $fillable = [
        'delivery_item_id',
        'inventory_cost_layer_id',
        'product_id',
        'allocated_qty',
        'unit_cost',
        'total_cost',
        'allocated_at',
    ];

    protected $casts = [
        'allocated_qty' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'allocated_at' => 'datetime',
    ];

    public function deliveryItem(): BelongsTo
    {
        return $this->belongsTo(DeliveryItem::class, 'delivery_item_id');
    }

    public function costLayer(): BelongsTo
    {
        return $this->belongsTo(InventoryCostLayer::class, 'inventory_cost_layer_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}

