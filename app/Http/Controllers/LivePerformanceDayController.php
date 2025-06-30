<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LivePerformanceDay;
use App\Models\LiveTargetDay;

class LivePerformanceDayController extends Controller
{
    // Hiển thị theo ngày
    public function daily(Request $request, $room_id)
    {
        // Nếu là admin thì cho xem mọi phòng
        if (auth()->guard('admin')->check()) {
            /** @var \App\Models\Admin $admin */
            $admin = auth()->guard('admin')->user();
        } else {
            /** @var \App\Models\Staff $user */
            $user = auth('web')->user();
    
            $hasAccess = $user->staffRoles()->where('room_id', $room_id)->exists();
            if (!$hasAccess) {
                abort(403, 'Bạn không có quyền truy cập phòng này.');
            }
        }
    
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());
    
        $dailyData = LivePerformanceDay::where('room_id', $room_id)
            ->where('type', 'daily')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();
    
        return view('live_performance.daily', compact('dailyData', 'room_id', 'startDate', 'endDate'));
    }
    

    // Hiển thị theo giờ
    public function hourly(Request $request, $room_id)
{
    // Nếu là admin thì cho xem mọi phòng
    if (auth()->guard('admin')->check()) {
        /** @var \App\Models\Admin $admin */
        $admin = auth()->guard('admin')->user();
    } else {
        /** @var \App\Models\Staff $user */
        $user = auth('web')->user();

        $hasAccess = $user->staffRoles()->where('room_id', $room_id)->exists();
        if (!$hasAccess) {
            abort(403, 'Bạn không có quyền truy cập phòng này.');
        }
    }

    $date = $request->input('date', now()->toDateString());
    $hourFrom = $request->input('hour_from');
    $hourTo = $request->input('hour_to');

    $query = LivePerformanceDay::where('room_id', $room_id)
        ->where('type', 'hourly')
        ->where('date', $date);

    if (is_numeric($hourFrom)) {
        $query->where('hour', '>=', (int)$hourFrom);
    }

    if (is_numeric($hourTo)) {
        $query->where('hour', '<=', (int)$hourTo);
    }

    $hourlyData = $query->orderBy('hour')->get()->keyBy('hour');

    return view('live_performance.hourly', compact('hourlyData', 'room_id', 'date'));
}


public function compareHourly(Request $request, $room_id)
{
    $date = $request->input('date', now()->toDateString());
    $hourFrom = $request->input('hour_from', 0);
    $hourTo = $request->input('hour_to', 23);

    // ✅ Lấy mục tiêu ngày
    $target = LiveTargetDay::where('room_id', $room_id)
        ->where('date', $date)
        ->first();

    $teamCount = $target->team_count ?? 0;
    $gmvTarget = $target->gmv_target ?? 0;
    $totalHours = $teamCount * 4;
    $targetPerHour = ($totalHours > 0 && $gmvTarget > 0) ? $gmvTarget / $totalHours : 0;

    // ✅ Lấy dữ liệu performance từng giờ
    $data = LivePerformanceDay::where('room_id', $room_id)
        ->where('type', 'hourly')
        ->where('date', $date)
        ->whereBetween('hour', [$hourFrom, $hourTo])
        ->orderBy('hour')
        ->get();

    $differences = [];

    for ($i = 1; $i < $data->count(); $i++) {
        $prev = $data[$i - 1];
        $curr = $data[$i];

        // Tính chênh lệch từng chỉ số
        $gmv_diff = $curr->gmv - $prev->gmv;
        $views_diff = $curr->views - $prev->views;
        $clicks_diff = $curr->product_clicks - $prev->product_clicks;
        $items_diff = $curr->items_sold - $prev->items_sold;
        $impressions_diff = $curr->live_impressions - $prev->live_impressions;

        // Tính lại hiệu suất từ chênh lệch
        $entry_rate = $impressions_diff > 0 ? round($views_diff / $impressions_diff * 100, 2) : null;
        $ctr = $views_diff > 0 ? round($clicks_diff / $views_diff * 100, 2) : null;
        $ctor = $clicks_diff > 0 ? round($items_diff / $clicks_diff * 100, 2) : null;

        $differences[] = (object)[
            'hour' => $curr->hour,
            'gmv' => $gmv_diff,
            'target_gmv' => $targetPerHour,
            'percent_achieved' => $targetPerHour > 0 ? round($gmv_diff / $targetPerHour * 100, 2) : null,
            'ads_total_cost' => $curr->ads_total_cost - $prev->ads_total_cost,
            'ads_manual_cost' => $curr->ads_manual_cost - $prev->ads_manual_cost,
            'ads_auto_cost' => $curr->ads_auto_cost - $prev->ads_auto_cost,
            'live_impressions' => $impressions_diff,
            'views' => $views_diff,
            'product_clicks' => $clicks_diff,
            'items_sold' => $items_diff,
            'comments' => $curr->comments - $prev->comments,
            'shares' => $curr->shares - $prev->shares,
            'entry_rate' => $entry_rate,
            'ctr' => $ctr,
            'ctor' => $ctor,
        ];
    }

  
    return view('room_report.report_hourly', [
    'room_id' => $room_id,
    'date' => $date,
    'hourFrom' => $hourFrom,
    'hourTo' => $hourTo,
    'differences' => collect($differences),
    'targetPerHour' => $targetPerHour,

]);
}

   public function snapshot(Request $request)
{
    // Lấy ngày được chọn từ request, mặc định là hôm nay
    $date = $request->input('date', now()->toDateString());

    // Lấy danh sách room và giờ mới nhất của mỗi room trong ngày đó
    $latestHours = \App\Models\LivePerformanceDay::where('type', 'hourly')
        ->where('date', $date)
        ->select('room_id')
        ->selectRaw('MAX(hour) as latest_hour')
        ->groupBy('room_id')
        ->get();

    $snapshot = collect();

    foreach ($latestHours as $item) {
        $record = \App\Models\LivePerformanceDay::where('type', 'hourly')
            ->where('room_id', $item->room_id)
            ->where('date', $date)
            ->where('hour', $item->latest_hour)
            ->first();

        if ($record) {
            $snapshot->push($record);
        }
    }

    // Sắp xếp theo GMV giảm dần
    $snapshot = $snapshot->sortByDesc('gmv')->values();

    return view('live_performance.snap_hourly', [
        'snapshot' => $snapshot,
        'selectedDate' => $date,
    ]);
}


}
