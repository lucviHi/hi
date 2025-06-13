<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdsManualDataDay extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'ads_manual_data_days';

    protected $fillable = [
        'room_id',
        'date', 'cost_usd', 'cost_local', 'cpc_usd', 'cpa_usd',
        'total_purchases', 'cost_per_payment', 'impressions', 'ctr', 'cpm',
        'cpc', 'clicks', 'conversions', 'cvr', 'cpa', 'roas_purchase',
        'roas_payment', 'roas_on_site', 'shopping_purchases', 'purchase_count',
        'cost_per_purchase', 'cost_per_shopping_purchase', 'total_payments',
        'cost_per_payment_repeat', 'video_views', 'video_views_2s', 'video_views_6s'
    ];

    protected $dates = ['date'];
    //protected $dates = ['deleted_at'];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
}
