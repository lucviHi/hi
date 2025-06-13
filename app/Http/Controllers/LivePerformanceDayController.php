<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LivePerformanceDay;

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

    
}
