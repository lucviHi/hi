<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Room extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'rooms';
    
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    public function staffRoles()
    {
        return $this->hasMany(StaffRole::class);
    }
}
