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
    public static function updateFromAuto($room_id, $date, $hour, $type, $cost, $gross_revenue, $roi)
{
    $auto_revenue = $gross_revenue * 0.8;

    $live = LivePerformanceDay::firstOrNew([
        'room_id' => $room_id,
        'date' => $date,
        'hour' => $type === 'hourly' ? intval($hour) : null,
        'type' => $type,
    ]);

    $live->ads_auto_cost = $cost;
    $live->gross_revenue = $gross_revenue;
    $live->auto_revenue = $auto_revenue;
    $live->roi = $roi;

    $live->ads_total_cost = ($live->ads_manual_cost ?? 0) + $cost;

    if ($live->ads_total_cost > 0) {
        $live->roas_total = round((($live->manual_revenue ?? 0) + $auto_revenue) / $live->ads_total_cost, 2);
    }

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
        $oldData = null // Thêm dữ liệu cũ nếu có

    
    ) {
        $record = \App\Models\LivePerformanceDay::firstOrNew([
            'room_id' => $room_id,
            'date' => $date,
            'type' => $type,
            'hour' => $type === 'hourly' ? intval($hour) : null
        ]);
    
        // $record->gmv = ($record->gmv ?? 0) + $gmv;
        // $record->items_sold = ($record->items_sold ?? 0) + $paid_orders;// Lấy paid_orders từ bảng streamer nhưng đang để bảng tổng là items_sold.
        // $record->views = ($record->views ?? 0) + $views;
        // $record->product_clicks = ($record->product_clicks ?? 0) + $product_clicks;
 
        // ✳️ Trừ dữ liệu cũ nếu có
    if ($oldData) {
        $record->gmv = max(0, ($record->gmv ?? 0) - $oldData['gmv']);
        $record->items_sold = max(0, ($record->items_sold ?? 0) - $oldData['paid_orders']);
        $record->views = max(0, ($record->views ?? 0) - $oldData['views']);
        $record->product_clicks = max(0, ($record->product_clicks ?? 0) - $oldData['product_clicks']);
        //$record->live_impressions = max(0, ($record->live_impressions ?? 0) - $oldData['live_impressions']);
    }

    // ✅ Cộng dữ liệu mới
    $record->gmv += $gmv;
    $record->items_sold += $paid_orders;
    $record->views += $views;
    $record->product_clicks += $product_clicks;
  
        // Tính hiển thị phiên live & entry_rate
        $liveImpressions = ($gmv > 0 && $gmvPer1kImpressions > 0)
            ? round($gmv / $gmvPer1kImpressions * 1000)
            : 0;
    
        $entryRate = ($liveImpressions > 0)
            ? round($record->views / $liveImpressions, 4)
            : 0;
    
        // Tính lại CTR, CTOR
        //$record->live_impressions = ($record->live_impressions ?? 0) + $liveImpressions;
    
        $record->live_impressions += $liveImpressions;
        $record->ctr = $record->views > 0
            ? round($record->views / $record->live_impressions, 4)
            : 0;
    
        $record->ctor = $record->product_clicks > 0
            ? round($record->items_sold / $record->product_clicks, 4)
            : 0;
    
        $record->entry_rate = $entryRate;
    
        $record->save();
    
       
    }
    
  
//  public static function updateFromStreamer($room_id, $date, $hour, $type)
// {
//     $query = \App\Models\StreamerDataDay::where('room_id', $room_id)
//         ->whereDate('start_time', $date);

//     if ($type === 'hourly') {
//         $query->where('hour', intval($hour)); // ✅ So sánh trực tiếp trường hour
//     }

//     $streamerSessions = $query->get();

//     if ($streamerSessions->isEmpty()) {
//         LivePerformanceDay::where([
//             'room_id' => $room_id,
//             'date' => $date,
//             'hour' => $type === 'hourly' ? intval($hour) : null,
//             'type' => $type,
//         ])->delete();
//         return;
//     }

//     $gmv = $streamerSessions->sum('gmv');
//     $paid_orders = $streamerSessions->sum('paid_orders');
//     $views = $streamerSessions->sum('views');
//     $product_clicks = $streamerSessions->sum('product_clicks');

//     $last = $streamerSessions->sortByDesc('start_time')->first();
//     $gmvPer1kImpressions = $last->gmv_per_1k_impressions ?? 0;

//     $liveImpressions = ($gmv > 0 && $gmvPer1kImpressions > 0)
//         ? round($gmv / $gmvPer1kImpressions * 1000)
//         : 0;

//     $entryRate = ($liveImpressions > 0)
//         ? round($views / $liveImpressions, 4)
//         : 0;

//     $ctr = $views > 0 ? round($product_clicks / $views, 4) : 0;
//     $ctor = $product_clicks > 0 ? round($paid_orders / $product_clicks, 4) : 0;

//     LivePerformanceDay::updateOrCreate([
//         'room_id' => $room_id,
//         'date' => $date,
//         'hour' => $type === 'hourly' ? intval($hour) : null,
//         'type' => $type,
//     ], [
//         'gmv' => $gmv,
//         'items_sold' => $paid_orders,
//         'views' => $views,
//         'product_clicks' => $product_clicks,
//         'live_impressions' => $liveImpressions,
//         'entry_rate' => $entryRate,
//         'ctr' => $ctr,
//         'ctor' => $ctor,
//     ]);
// }



}
