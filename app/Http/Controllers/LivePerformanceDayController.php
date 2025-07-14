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

   public function updateDealCost(Request $request)
{
    $id = $request->input('id');
    $raw = $request->input('deal_cost');
    $cleanCost = floatval(str_replace(',', '', $raw)); // xoá dấu phẩy để tránh lỗi

    $record = LivePerformanceDay::find($id);
    if ($record) {
        $record->deal_cost = $cleanCost;
        $record->total_cost = ($record->ads_total_cost ?? 0) + $cleanCost;
        $record->save();
    }

    return back()->with('success', 'Đã cập nhật chi phí deal.');
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

// public function snapshot(Request $request)
// {
//     $date = $request->input('date', now()->toDateString());
//     $currentHour = now()->timezone('Asia/Ho_Chi_Minh')->hour;

//     // Lấy toàn bộ room có tồn tại trong hệ thống
//     $rooms = \App\Models\Room::with('project')->get();

//     $data = $rooms->map(function ($room) use ($date) {
//         $latest = \App\Models\LivePerformanceDay::where('room_id', $room->id)
//             ->where('date', $date)
//             ->where('type', 'hourly')
//             ->orderByDesc('hour')
//             ->first();

//         $target = \App\Models\LiveTargetDay::where('room_id', $room->id)
//             ->where('date', $date)
//             ->first();

//         $gmv = $latest?->gmv ?? 0;
//         $gmvTarget = $target?->gmv_target ?? 0;

//         return (object)[
//             'room' => $room,
//             'room_id' => $room->id,
//             'date' => $date,
//             'hour' => $latest?->hour,
//             'gmv' => $gmv,
//             'gmv_target' => $gmvTarget,
//             'percent_achieved' => ($gmvTarget > 0) ? round($gmv / $gmvTarget * 100, 2) : null,
//             'ads_total_cost' => $latest?->ads_total_cost ?? 0,
//             'ads_manual_cost' => $latest?->ads_manual_cost ?? 0,
//             'ads_auto_cost' => $latest?->ads_auto_cost ?? 0,
//             'live_impressions' => $latest?->live_impressions ?? 0,
//             'views' => $latest?->views ?? 0,
//             'product_clicks' => $latest?->product_clicks ?? 0,
//             'items_sold' => $latest?->items_sold ?? 0,
//             'ctr' => $latest?->ctr ?? null,
//             'ctor' => $latest?->ctor ?? null,
//         ];
//     });

//     $data = $data->sortByDesc('gmv')->values();

//     return view('live_performance.snap_hourly', [
//         'snapshot' => $data,
//         'selectedDate' => $date,
//         'currentHour' => $currentHour,
//     ]);
// }
public function snapshot(Request $request) 
{
    $date = $request->input('date', now()->toDateString());
    $currentHour = now()->timezone('Asia/Ho_Chi_Minh')->hour;

    $projectId = $request->input('project_id');
    $roomId = $request->input('room_id');
    $filterValidHour = $request->boolean('filter_hour_before_now', false); // checkbox lọc

    $allRooms = \App\Models\Room::with('project')->get();

    // Lọc room theo project và room
    $rooms = $allRooms
        ->when($projectId, fn($q) => $q->where('project_id', $projectId))
        ->when($roomId, fn($q) => $q->where('id', $roomId));

    $data = $rooms->map(function ($room) use ($date, $currentHour, $filterValidHour) {
        $latest = \App\Models\LivePerformanceDay::where('room_id', $room->id)
            ->where('date', $date)
            ->where('type', 'hourly')
            ->orderByDesc('hour')
            ->first();

        // Nếu lọc theo giờ hợp lệ, chỉ hiện kênh có hour < currentHour
        if ($filterValidHour && (!$latest || $latest->hour >= $currentHour)) {
            return null; // loại khỏi danh sách hiển thị
        }

        $target = \App\Models\LiveTargetDay::where('room_id', $room->id)
            ->where('date', $date)
            ->first();

        $gmv = $latest?->gmv ?? 0;
        $gmvTarget = $target?->gmv_target ?? 0;

        return (object)[
            'room' => $room,
            'room_id' => $room->id,
            'date' => $date,
            'hour' => $latest?->hour,
            'gmv' => $gmv,
            'gmv_target' => $gmvTarget,
            'percent_achieved' => ($gmvTarget > 0) ? round($gmv / $gmvTarget * 100, 2) : null,
            'ads_total_cost' => $latest?->ads_total_cost ?? 0,
            'ads_manual_cost' => $latest?->ads_manual_cost ?? 0,
            'ads_auto_cost' => $latest?->ads_auto_cost ?? 0,
            'live_impressions' => $latest?->live_impressions ?? 0,
            'views' => $latest?->views ?? 0,
            'product_clicks' => $latest?->product_clicks ?? 0,
            'items_sold' => $latest?->items_sold ?? 0,
            'ctr' => $latest?->ctr ?? null,
            'ctor' => $latest?->ctor ?? null,
        ];
    })->filter(); // loại null nếu có bật lọc

    $data = $data->sortByDesc('gmv')->values();

    return view('live_performance.snap_hourly', [
        'snapshot' => $data,
        'selectedDate' => $date,
        'currentHour' => $currentHour,
        'projects' => \App\Models\Project::all(),
        'rooms' => $allRooms,
        'selectedProject' => $projectId,
        'selectedRoom' => $roomId,
        'filterValidHour' => $filterValidHour,
    ]);
}

public function snapshotDailyRange(Request $request)
{
    $from = $request->input('from_date', now()->timezone('Asia/Ho_Chi_Minh')->subDay()->toDateString());
    $to = $request->input('to_date', now()->timezone('Asia/Ho_Chi_Minh')->subDay()->toDateString());
    $projectId = $request->input('project_id');
    $roomId = $request->input('room_id');

    $allRooms = \App\Models\Room::with('project')->get();

    $rooms = $allRooms->when($projectId, fn($q) => $q->where('project_id', $projectId))
                      ->when($roomId, fn($q) => $q->where('id', $roomId));

    $data = $rooms->map(function ($room) use ($from, $to) {
        $records = LivePerformanceDay::where('room_id', $room->id)
            ->whereBetween('date', [$from, $to])
            ->where('type', 'daily')
            ->get();

        $targets = \App\Models\LiveTargetDay::where('room_id', $room->id)
            ->whereBetween('date', [$from, $to])
            ->get();

        $gmv = $records->sum('gmv');
        $ads = $records->sum('ads_total_cost');
        $liveImpressions = $records->sum('live_impressions');
        $views = $records->sum('views');
        $clicks = $records->sum('product_clicks');
        $items = $records->sum('items_sold');
        $gmvTarget = $targets->sum('gmv_target');
        $dealCost = $records->sum('deal_cost');
        $totalCost = $ads + $dealCost;
        return (object)[
            'room' => $room,
            'room_id' => $room->id,
            'gmv' => $gmv,
            'gmv_target' => $gmvTarget,
            'percent_achieved' => ($gmvTarget > 0) ? round($gmv / $gmvTarget * 100, 2) : null,
            'ads_total_cost' => $ads,
            'deal_cost' => $dealCost,
            'total_cost' => $totalCost,
            'live_impressions' => $liveImpressions,
            'views' => $views,
            'product_clicks' => $clicks,
            'items_sold' => $items,
            'entry_rate' => $liveImpressions > 0 ? round($views / $liveImpressions * 100, 2) : null,
            'ctr' => $views > 0 ? round($clicks / $views * 100, 2) : null,
            'ctor' => $clicks > 0 ? round($items / $clicks * 100, 2) : null,
        ];
    });

    $data = $data->sortByDesc('gmv')->values();

    return view('live_performance.snap_daily_range', [
        'snapshot' => $data,
        'from' => $from,
        'to' => $to,
        'projects' => \App\Models\Project::all(),
        'rooms' => $allRooms,
        'selectedProject' => $projectId,
        'selectedRoom' => $roomId,
    ]);
}

}
