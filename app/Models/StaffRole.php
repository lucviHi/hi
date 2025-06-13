<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffRole extends Model
{
    protected $guarded = [];

    protected $table = 'staff_roles';
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

}