<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class AdsAutoDay extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ads_auto_data_days';

    protected $guarded = [];


    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
}
