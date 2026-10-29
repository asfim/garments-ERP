<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function stock()
    {
        return $this->hasOne(Stock::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function productions()
    {
        return $this->hasMany(Production::class);
    }

    public function getCurrentStockAttribute()
    {
        return $this->stock ? $this->stock->current_stock : 0;
    }
}
