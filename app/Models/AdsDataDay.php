<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdsDataDay extends Model
{
    use HasFactory;

    protected $table = 'ads_data_days';

    protected $guarded = [];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function adsGmvMax()
    {
        return $this->hasMany(AdsGmvMaxDataDay::class, 'room_id', 'room_id');
    }

    public function adsManual()
    {
        return $this->hasMany(AdsManualDataDay::class, 'room_id', 'room_id');
    }
}
