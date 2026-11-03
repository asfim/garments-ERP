<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrBiometricLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'punch_time' => 'datetime',
    ];

    public function device()
    {
        return $this->belongsTo(HrBiometricDevice::class, 'device_id');
    }

    public function employee()
    {
        return $this->belongsTo(HrEmployee::class, 'employee_id');
    }
}
