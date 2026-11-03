<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrBiometricDevice extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'last_sync_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function logs()
    {
        return $this->hasMany(HrBiometricLog::class, 'device_id');
    }

    public function getIpAddressAttribute()
    {
        return $this->attributes['device_ip'] ?? '';
    }

    public function setIpAddressAttribute($value)
    {
        $this->attributes['device_ip'] = $value;
    }

    public function getModelAttribute()
    {
        return $this->attributes['device_model'] ?? '';
    }

    public function setModelAttribute($value)
    {
        $this->attributes['device_model'] = $value;
    }

    public function getLocationAttribute()
    {
        return $this->attributes['notes'] ?? '';
    }

    public function setLocationAttribute($value)
    {
        $this->attributes['notes'] = $value;
    }
}
