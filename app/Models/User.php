<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Linked Employee profile (if user is an employee)
     */
    public function employee()
    {
        return $this->hasOne(HrEmployee::class, 'user_id');
    }

    /**
     * Check if user is an employee
     */
    public function isEmployee(): bool
    {
        return $this->role === 'employee' || $this->hasRole('employee') || $this->hasRole('Employee');
    }

    /**
     * Check if user is admin / super admin
     */
    public function isAdmin(): bool
    {
        if ($this->hasRole('Super Admin') || $this->hasRole('Admin') || $this->role === 'admin') {
            return true;
        }
        return false;
    }
}
