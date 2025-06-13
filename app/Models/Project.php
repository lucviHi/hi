<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $guarded = [];
    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
