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

//    public function snapshot(Request $request)
// {
//     // Lấy ngày được chọn từ request, mặc định là hôm nay
//     $date = $request->input('date', now()->toDateString());

//     // Lấy danh sách room và giờ mới nhất của mỗi room trong ngày đó
//     $latestHours = \App\Models\LivePerformanceDay::where('type', 'hourly')
//         ->where('date', $date)
//         ->select('room_id')
//         ->selectRaw('MAX(hour) as latest_hour')
//         ->groupBy('room_id')
//         ->get();

//     $snapshot = collect();

//     foreach ($latestHours as $item) {
//         $record = \App\Models\LivePerformanceDay::where('type', 'hourly')
//             ->where('room_id', $item->room_id)
//             ->where('date', $date)
//             ->where('hour', $item->latest_hour)
//             ->first();

//         if ($record) {
//             $snapshot->push($record);
//         }
//     }

//     // Sắp xếp theo GMV giảm dần
//     $snapshot = $snapshot->sortByDesc('gmv')->values();

//     return view('live_performance.snap_hourly', [
//         'snapshot' => $snapshot,
//         'selectedDate' => $date,
//     ]);
// }
public function snapshot(Request $request)
{
    $date = $request->input('date', now()->toDateString());
    $currentHour = now()->timezone('Asia/Ho_Chi_Minh')->hour;

    // Lấy toàn bộ room có tồn tại trong hệ thống
    $rooms = \App\Models\Room::with('project')->get();

   $data = $rooms->map(function ($room) use ($date) {
    $latest = \App\Models\LivePerformanceDay::where('room_id', $room->id)
        ->where('date', $date)
        ->where('type', 'hourly')
        ->orderByDesc('hour')
        ->first();

    return (object)[ // 👈 chuyển array => object
        'room' => $room,
        'room_id' => $room->id,
        'date' => $date,
        'hour' => $latest?->hour,
        'gmv' => $latest?->gmv ?? 0,
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
});


    // Sắp xếp theo GMV giảm dần
    $data = $data->sortByDesc('gmv')->values();

    return view('live_performance.snap_hourly', [
        'snapshot' => $data,
        'selectedDate' => $date,
        'currentHour' => $currentHour,
    ]);
}

public function snapshotDailyRange(Request $request)
{
    $from = $request->input('from_date', now()->timezone('Asia/Ho_Chi_Minh')->subDay()->toDateString());
    $to = $request->input('to_date', now()->timezone('Asia/Ho_Chi_Minh')->subDay()->toDateString());
    $projectId = $request->input('project_id');
    $roomId = $request->input('room_id');

    // Lấy danh sách tất cả rooms (để render filter dropdown)
    $allRooms = \App\Models\Room::with('project')->get();

    // Lọc danh sách room theo project/room nếu có
    $rooms = $allRooms->when($projectId, fn($q) => $q->where('project_id', $projectId))
                      ->when($roomId, fn($q) => $q->where('id', $roomId));

    $data = $rooms->map(function ($room) use ($from, $to) {
        $records = LivePerformanceDay::where('room_id', $room->id)
            ->whereBetween('date', [$from, $to])
            ->where('type', 'daily')
            ->get();

        $gmv = $records->sum('gmv');
        $ads = $records->sum('ads_total_cost');
        $liveImpressions = $records->sum('live_impressions');
        $views = $records->sum('views');
        $clicks = $records->sum('product_clicks');
        $items = $records->sum('items_sold');

        return (object)[
            'room' => $room,
            'room_id' => $room->id,
            'gmv' => $gmv,
            'ads_total_cost' => $ads,
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
