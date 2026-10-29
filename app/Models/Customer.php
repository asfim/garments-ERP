<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $guarded = [];

    public function orders()
    {
        return $this->hasMany(Order::class)->orderBy('order_date', 'desc');
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class)->orderBy('delivery_date', 'desc');
    }

    public function getTotalOrderedAttribute()
    {
        return $this->orders()->sum('total_amount');
    }

    public function getTotalPaidAttribute()
    {
        return $this->orders()->sum('paid_amount');
    }

    public function getTotalDueAttribute()
    {
        return $this->orders()->sum('due_amount');
    }
}
