<?php
namespace App\Services;

use App\Models\LivePerformanceDay;
use Illuminate\Support\Facades\DB;

class LivePerformanceAggregator
{
    public static function updateFromManual($room_id, $date, $hour, $type, $cost_vnd, $manual_revenue, $roas_manual)
    {
        $live = LivePerformanceDay::firstOrNew([
            'room_id' => $room_id,
            'date' => $date,
            'hour' => $type === 'hourly' ? intval($hour) : null,
            'type' => $type,
        ]);
    
        $live->ads_manual_cost = $cost_vnd;
        $live->manual_revenue = $manual_revenue;
        $live->roas_manual = $roas_manual;
    
        // Cập nhật tổng chi phí quảng cáo
        $live->ads_total_cost = $cost_vnd + ($live->ads_auto_cost ?? 0);
    
        // Tính lại roas tổng nếu có đủ chi phí
        if ($live->ads_total_cost > 0) {
            $live->roas_total = round(($manual_revenue + ($live->auto_revenue ?? 0)) / $live->ads_total_cost, 2);
        }
    
        $live->save();
    }
    
    // sau này thêm:
    // public static function updateFromAuto(...) {}
    public static function updateFromAuto($room_id, $date, $hour, $type, $cost)
{
    //$auto_revenue = $gross_revenue * 0.8;

    $live = LivePerformanceDay::firstOrNew([
        'room_id' => $room_id,
        'date' => $date,
        'hour' => $type === 'hourly' ? intval($hour) : null,
        'type' => $type,
    ]);

    $live->ads_auto_cost = $cost;
    //$live->gross_revenue = $gross_revenue;
    //$live->auto_revenue = $auto_revenue;
    //$live->roi = $roi;

    $live->ads_total_cost = ($live->ads_manual_cost ?? 0) + $cost;

    // if ($live->ads_total_cost > 0) {
    //     $live->roas_total = round((($live->manual_revenue ?? 0) + $auto_revenue) / $live->ads_total_cost, 2);
    // }

    $live->save();
}

   public static function updateFromStreamer(
    $room_id,
    $date,
    $hour,
    $type,
    $gmv,
    $paid_orders,
    $views,
    $gmvPer1kImpressions = null,
    $product_clicks = 0,
    $oldData = null
) {
    $record = \App\Models\LivePerformanceDay::firstOrNew([
        'room_id' => $room_id,
        'date' => $date,
        'type' => $type,
        'hour' => $type === 'hourly' ? intval($hour) : null
    ]);

    if ($oldData) {
        $record->gmv = max(0, ($record->gmv ?? 0) - $oldData['gmv']);
        $record->items_sold = max(0, ($record->items_sold ?? 0) - $oldData['paid_orders']);
        $record->views = max(0, ($record->views ?? 0) - $oldData['views']);
        $record->product_clicks = max(0, ($record->product_clicks ?? 0) - $oldData['product_clicks']);
    }

    $record->gmv += $gmv;
    $record->items_sold += $paid_orders;
    $record->views += $views;
    $record->product_clicks += $product_clicks;

    $liveImpressions = ($gmv > 0 && $gmvPer1kImpressions > 0)
        ? round($gmv / $gmvPer1kImpressions * 1000)
        : 0;

    $record->live_impressions += $liveImpressions;

    $record->save(); // 
}
public static function updateStreamerSummary($room_id, $date, $hour = null, $type = 'daily')
{
    $query = \App\Models\StreamerDataDay::where('room_id', $room_id)
        ->whereDate('start_time', $date)
        ->where('type', $type);

    // ✅ THÊM điều kiện lọc giờ nếu là loại hourly
    if ($type === 'hourly' && $hour !== null) {
        $query->where('hour', intval($hour)); // dùng trường hour trong bảng streamer_data_days
    }

    $sessions = $query->get();

    $gmv = $sessions->sum('gmv');
    $paid_orders = $sessions->sum('paid_orders');
    $views = $sessions->sum('views');
    $product_clicks = $sessions->sum('product_clicks');

    // ✅ Tính live impressions (không cộng dồn sai)
    $liveImpressions = $sessions->reduce(function ($carry, $item) {
        if ($item->gmv > 0 && $item->gmv_per_1k_impressions > 0) {
            return $carry + round($item->gmv / $item->gmv_per_1k_impressions * 1000);
        }
        return $carry;
    }, 0);

    // ✅ Ghi đè hoàn toàn (KHÔNG cộng dồn)
    $record = \App\Models\LivePerformanceDay::firstOrNew([
        'room_id' => $room_id,
        'date' => $date,
        'type' => $type,
        'hour' => $type === 'hourly' ? intval($hour) : null,
    ]);

    $record->gmv = $gmv;
    $record->items_sold = $paid_orders;
    $record->views = $views;
    $record->product_clicks = $product_clicks;
    $record->live_impressions = $liveImpressions;

    $record->entry_rate = $liveImpressions > 0 ? round($views / $liveImpressions, 4) : 0;
    $record->ctr = $views > 0 ? round($product_clicks / $views, 4) : 0;
    $record->ctor = $product_clicks > 0 ? round($paid_orders / $product_clicks, 4) : 0;

    $record->save();
}




}
