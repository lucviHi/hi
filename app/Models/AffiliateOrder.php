<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AffiliateOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'order_id',
        'product_id',
        'product_name',
        'sku',
        'sku_id',
        'seller_sku',
        'price',
        'payment_amount',
        'quantity',
        'order_status',
        'content_type',
        'standard_commission_rate',
        'ordered_at',
    ];

    protected $dates = ['ordered_at'];

    // Quan hệ với bảng Project
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
