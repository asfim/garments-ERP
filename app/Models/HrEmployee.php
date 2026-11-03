<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrEmployee extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'joining_date' => 'date',
        'basic_salary' => 'decimal:2',
        'house_rent' => 'decimal:2',
        'medical_allowance' => 'decimal:2',
        'conveyance_allowance' => 'decimal:2',
        'gross_salary' => 'decimal:2',
    ];

    public function department()
    {
        return $this->belongsTo(HrDepartment::class, 'department_id');
    }

    public function designation()
    {
        return $this->belongsTo(HrDesignation::class, 'designation_id');
    }

    public function attendances()
    {
        return $this->hasMany(HrAttendance::class, 'employee_id');
    }

    public function leaves()
    {
        return $this->hasMany(HrLeave::class, 'employee_id');
    }

    public function payrolls()
    {
        return $this->hasMany(HrPayroll::class, 'employee_id');
    }

    public function advances()
    {
        return $this->hasMany(HrAdvance::class, 'employee_id');
    }

    public function biometricLogs()
    {
        return $this->hasMany(HrBiometricLog::class, 'employee_id');
    }
}

