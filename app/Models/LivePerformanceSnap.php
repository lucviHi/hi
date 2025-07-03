<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LivePerformanceSnap extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'date',
        'hour',
        'type',
        'main_host_id',
        'support_host_id',
        'gmv',
        'ads_total_cost',
        'views',
        'live_impressions',
        'items_sold',
        'product_clicks',
        'comments',
        'shares',
        'ctr',
        'ctor',
        'entry_rate',
    ];

    // Quan hệ
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function mainHost()
    {
        return $this->belongsTo(Staff::class, 'main_host_id');
    }

    public function supportHost()
    {
        return $this->belongsTo(Staff::class, 'support_host_id');
    }
}
