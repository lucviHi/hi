<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdsGmvMaxDataDay extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ads_gmv_max_data_days';

    protected $guarded = [];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
}
