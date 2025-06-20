<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LivePerformanceSnapshot extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'room_id',
        'date',
        'hour',
        'type',
        'live_session_name',
        'ads_manual_cost',
        'ads_auto_cost',
        'ads_total_cost',
        'gmv',
        'roi',
        'impressions',
        'views',
        'product_clicks',
        'items_sold',
        'comments',
        'shares',
        'ctr',
        'ctor',
    ];

    protected $casts = [
        'date' => 'date',
        'hour' => 'datetime:H:i',
        'ads_manual_cost' => 'decimal:2',
        'ads_auto_cost' => 'decimal:2',
        'ads_total_cost' => 'decimal:2',
        'gmv' => 'decimal:2',
        'roi' => 'decimal:2',
        'ctr' => 'decimal:2',
        'ctor' => 'decimal:2',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
