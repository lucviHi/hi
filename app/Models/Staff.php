<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Staff extends Authenticatable
{
    use Notifiable;
    protected $table = 'staffs';

    protected $fillable = [
        'staff_code', 'name', 'email', 'password'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    // Quan hệ với bảng trung gian staff_roles
    public function staffRoles()
    {
        return $this->hasMany(StaffRole::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'staff_roles');
    }

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'staff_roles');
    }
}
