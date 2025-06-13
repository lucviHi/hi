<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StreamerDataDay extends Model {
    use HasFactory;
    protected $table = 'streamer_data_days';
    // protected $fillable = [
    //     'room_id', 'live_name', 'start_time', 'duration', 'total_revenue',
    //     'gmv', 'items_sold', 'customers', 'avg_price', 'paid_orders',
    //     'gmv_per_1k_impressions', 'gmv_per_1k_views', 'views', 'viewers',
    //     'max_viewers', 'new_followers', 'avg_watch_time', 'likes', 'comments',
    //     'shares', 'product_displays', 'product_clicks', 'ctr', 'ctor'
    // ];
    // protected $dates = ['start_time']; // Hỗ trợ xóa mềm
    protected $guarded = [];
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
}
