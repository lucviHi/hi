<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveTargetDay extends Model
{
    protected $fillable = [
        'room_id', 'date', 'gmv_target', 'cost_limit', 'team_count', 'day_type', 'note'
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
