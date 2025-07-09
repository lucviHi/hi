<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Project;
use App\Models\LivePerformanceDay;
use App\Models\LiveTargetDay;
use App\Models\LivePerformanceSnap;
use App\Models\Staff;
use Carbon\Carbon;
class RoomController extends Controller
{
    // 1️⃣ Danh sách Room
    public function index()
    {
        $rooms = Room::with('project')->paginate(10); // Lấy Room kèm Project
        return view('rooms.index', compact('rooms'));
        
    }
    
    public function create()
    {
        $projects = Project::all();
        return view('rooms.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
        ]);

        Room::create($request->all());

        return redirect()->route('rooms.index')->with('success', 'Phòng đã được tạo.');
    }

    public function edit(Room $room)
    {
        $projects = Project::all();
        return view('rooms.edit', compact('room', 'projects'));
    }

    public function update(Request $request, Room $room)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
        ]);

        $room->update($request->all());

        return redirect()->route('rooms.index')->with('success', 'Phòng đã được cập nhật.');
    }

    public function destroy(Room $room)
    {
        $room->delete();

        return redirect()->route('rooms.index')->with('success', 'Phòng đã được xóa.');
    }

    public function show_live($room_id)
    {
        // Tìm phòng theo ID, nếu không tìm thấy sẽ trả về lỗi 404
        $room = Room::findOrFail($room_id);

        // Trả về view 'rooms.show' với dữ liệu phòng
        return view('rooms.show', compact('room'));
    }
    public function report_daily($room_id)
    {
        // Tìm phòng theo ID, nếu không tìm thấy sẽ trả về lỗi 404
        $room = Room::findOrFail($room_id);

        // Trả về view 'rooms.show' với dữ liệu phòng
        return view('room_report.report_daily', compact('room'));
    }
    public function report_hourly($room_id)
    {
        // Tìm phòng theo ID, nếu không tìm thấy sẽ trả về lỗi 404
        $room = Room::findOrFail($room_id);

        // Trả về view 'rooms.show' với dữ liệu phòng
        return view('room_report.report_hourly', compact('room'));
    }





public function show($room_id)
{
    // Phân quyền
      if (auth()->guard('admin')->check()) {
            /** @var \App\Models\Admin $admin */
            $admin = auth()->guard('admin')->user();
            $room = \App\Models\Room::findOrFail($room_id);
        } else {
            /** @var \App\Models\Staff $user */
            $user = auth()->guard('web')->user();
            $room = $user->rooms()->where('rooms.id', $room_id)->firstOrFail();
        }
    
    // $room = auth()->guard('admin')->check()
    //     ? Room::findOrFail($room_id)
    //     : auth()->guard('web')->user()->rooms()->where('rooms.id', $room_id)->firstOrFail();

  $today = now()->toDateString();
$yesterday = now()->subDay()->toDateString();
$startOfMonth = now()->startOfMonth()->toDateString();
$endOfMonth = now()->endOfMonth()->toDateString();

// === 1. Doanh thu hôm nay (latest hourly)
$latestToday = LivePerformanceDay::where('room_id', $room_id)
    ->whereDate('date', $today)
    ->where('type', 'hourly')
    ->orderByDesc('hour')
    ->first();

$todayRevenue = $latestToday?->gmv ?? 0;
$todayCost = $latestToday?->ads_total_cost ?? 0;
$todayCostPercent = $todayRevenue > 0 ? round(($todayCost / $todayRevenue) * 100, 2) : 0;

// === 2. Doanh thu hôm qua (latest hourly)
$latestYesterday = LivePerformanceDay::where('room_id', $room_id)
    ->whereDate('date', $yesterday)
    ->where('type', 'daily')
    ->first();

$yesterdayRevenue = $latestYesterday?->gmv ?? 0;

// === 3. Doanh thu + chi phí tháng (daily)
$monthRevenue = LivePerformanceDay::where('room_id', $room_id)
    ->whereBetween('date', [$startOfMonth, $endOfMonth])
    ->where('type', 'daily')
    ->sum('gmv') ?? 0;

$monthCost = LivePerformanceDay::where('room_id', $room_id)
    ->whereBetween('date', [$startOfMonth, $endOfMonth])
    ->where('type', 'daily')
    ->sum('ads_total_cost') ?? 0;

$monthCostPercent = $monthRevenue > 0 ? round(($monthCost / $monthRevenue) * 100, 2) : 0;

// === 4. % đạt mục tiêu hôm nay
$todayTarget = LiveTargetDay::where('room_id', $room_id)
    ->whereDate('date', $today)
    ->sum('gmv_target') ?? 0;
$todayTargetPercent = $todayTarget > 0 ? round(($todayRevenue / $todayTarget) * 100, 2) : 0;

// === 5. % đạt mục tiêu tháng
$monthTarget = LiveTargetDay::where('room_id', $room_id)
    ->whereBetween('date', [$startOfMonth, $endOfMonth])
    ->sum('gmv_target') ?? 0;
$monthTargetPercent = $monthTarget > 0 ? round(($monthRevenue / $monthTarget) * 100, 2) : 0;

// === 6. Biểu đồ GMV theo giờ (hourly only)
$todayData = LivePerformanceDay::where('room_id', $room_id)
    ->whereDate('date', $today)
    ->where('type', 'hourly')
    ->get()
    ->groupBy(fn($item) => str_pad($item->hour, 2, '0', STR_PAD_LEFT) . ':00')
    ->map(fn($group) => $group->sum('gmv') ?? 0);

$yesterdayData = LivePerformanceDay::where('room_id', $room_id)
    ->whereDate('date', $yesterday)
    ->where('type', 'hourly')
    ->get()
    ->groupBy(fn($item) => str_pad($item->hour, 2, '0', STR_PAD_LEFT) . ':00')
    ->map(fn($group) => $group->sum('gmv') ?? 0);

$allHours = collect(range(0, 23))->map(fn($h) => str_pad($h, 2, '0', STR_PAD_LEFT) . ':00');

// === Top 3 host chính hôm nay ===
$topMainHostsToday = LivePerformanceSnap::where('room_id', $room_id)
    ->whereDate('date', $today)
    ->whereNotNull('main_host_id')
    ->groupBy('main_host_id')
    ->selectRaw('main_host_id as staff_id, SUM(gmv) as total_gmv')
    ->orderByDesc('total_gmv')
    ->take(3)
    ->get()
    ->map(fn($h) => [
        'name' => optional(Staff::find($h->staff_id))->name ?? 'Không rõ',
        'gmv' => $h->total_gmv
    ]);

// === Top 3 host chính tháng này ===
$topMainHostsMonth = LivePerformanceSnap::where('room_id', $room_id)
    ->whereBetween('date', [$startOfMonth, $endOfMonth])
    ->whereNotNull('main_host_id')
    ->groupBy('main_host_id')
    ->selectRaw('main_host_id as staff_id, SUM(gmv) as total_gmv')
    ->orderByDesc('total_gmv')
    ->take(3)
    ->get()
    ->map(fn($h) => [
        'name' => optional(Staff::find($h->staff_id))->name ?? 'Không rõ',
        'gmv' => $h->total_gmv
    ]);

  
// === Chuẩn bị biểu đồ GMV & % Ads/Gmv tuần này ===
$startOfWeek = now()->startOfWeek()->toDateString();
$endOfWeek = now()->endOfWeek()->toDateString();

$weeklyStats = LivePerformanceDay::where('room_id', $room_id)
    ->whereBetween('date', [$startOfWeek, $endOfWeek])
    ->where('type', 'daily')
    ->selectRaw('date, SUM(gmv) as gmv, SUM(ads_total_cost) as cost')
    ->groupBy('date')
    ->orderBy('date')
    ->get()
    ->map(function ($day) {
        return [
            'date' => $day->date,
            'gmv' => (float) $day->gmv,
            'cost_percent' => $day->gmv > 0 ? round(($day->cost / $day->gmv) * 100, 2) : 0
        ];
    });

$monthlyStats = LivePerformanceDay::where('room_id', $room_id)
    ->whereBetween('date', [$startOfMonth, $endOfMonth])
    ->where('type', 'daily')
    ->selectRaw('date, SUM(gmv) as gmv, SUM(ads_total_cost) as cost')
    ->groupBy('date')
    ->orderBy('date')
    ->get()
    ->map(function ($day) {
        return [
            'date' => $day->date,
            'gmv' => (float) $day->gmv,
            'cost_percent' => $day->gmv > 0 ? round(($day->cost / $day->gmv) * 100, 2) : 0
        ];
    });


    return view('rooms.dashboard', compact(
    'room',
    'todayRevenue', 'yesterdayRevenue',
    'todayCostPercent', 'monthRevenue', 'monthCostPercent',
    'todayTargetPercent', 'monthTargetPercent',
    'todayData', 'yesterdayData', 'allHours',
    'topMainHostsToday', 'topMainHostsMonth',
    'weeklyStats', 'monthlyStats'


));

}

   
    
}
