<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LivePerformanceDay extends Model
{
    use HasFactory;

    protected $table = 'live_performance_days';

    protected $fillable = [
        'room_id',
        'date',
        'hour',
        'type',
        'live_session_name',
        'ads_manual_cost',
        'ads_auto_cost',
        'ads_total_cost',
        'manual_revenue',
        'auto_revenue',
        'gross_revenue',
        'gmv',
        'roas_manual',
        'roi',
        'roas_total',
        'items_sold',
        'views',
        'comments',
        'shares',
        'ctr',
        'ctor',
        'commission',
        'note',
    ];
    public function room()
{
    return $this->belongsTo(Room::class);
}

}
