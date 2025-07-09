<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Project;
use App\Models\LivePerformanceDay;
use App\Models\LiveTargetDay;
use App\Models\LivePerformanceSnap;
use App\Models\Staff;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();
        $startOfMonth = now()->startOfMonth()->toDateString();
        $endOfMonth = now()->endOfMonth()->toDateString();

        $selectedProjectId = $request->input('project_id');
        $selectedRoomId = $request->input('room_id');

        $projects = Project::all();
        $rooms = Room::when($selectedProjectId, fn($q) => $q->where('project_id', $selectedProjectId))->get();

        $roomIds = $selectedRoomId
            ? [$selectedRoomId]
            : $rooms->pluck('id');

        // Tổng hợp chỉ số chung
        $todayRevenue = LivePerformanceDay::whereIn('room_id', $roomIds)->whereDate('date', $today)->sum('gmv');
        $yesterdayRevenue = LivePerformanceDay::whereIn('room_id', $roomIds)->whereDate('date', $yesterday)->sum('gmv');

        $todayCost = LivePerformanceDay::whereIn('room_id', $roomIds)->whereDate('date', $today)->sum('ads_total_cost');
        $todayCostPercent = $todayRevenue > 0 ? round(($todayCost / $todayRevenue) * 100, 2) : 0;

        $monthRevenue = LivePerformanceDay::whereIn('room_id', $roomIds)->whereBetween('date', [$startOfMonth, $endOfMonth])->sum('gmv');
        $monthCost = LivePerformanceDay::whereIn('room_id', $roomIds)->whereBetween('date', [$startOfMonth, $endOfMonth])->sum('ads_total_cost');
        $monthCostPercent = $monthRevenue > 0 ? round(($monthCost / $monthRevenue) * 100, 2) : 0;

        $todayTarget = LiveTargetDay::whereIn('room_id', $roomIds)->whereDate('date', $today)->sum('gmv_target');
        $todayTargetPercent = $todayTarget > 0 ? round(($todayRevenue / $todayTarget) * 100, 2) : 0;

        $monthTarget = LiveTargetDay::whereIn('room_id', $roomIds)->whereBetween('date', [$startOfMonth, $endOfMonth])->sum('gmv_target');
        $monthTargetPercent = $monthTarget > 0 ? round(($monthRevenue / $monthTarget) * 100, 2) : 0;

$todayDataRaw = LivePerformanceDay::whereIn('room_id', $roomIds)
    ->whereDate('date', $today)
    ->get();

$yesterdayDataRaw = LivePerformanceDay::whereIn('room_id', $roomIds)
    ->whereDate('date', $yesterday)
    ->get();

$allHours = collect(range(0, 23))->map(fn($h) => str_pad($h, 2, '0', STR_PAD_LEFT) . ':00');

$todayData = $allHours->mapWithKeys(function ($hour) use ($todayDataRaw) {
    return [$hour => $todayDataRaw->where('hour', (int) substr($hour, 0, 2))->sum('gmv')];
});

$yesterdayData = $allHours->mapWithKeys(function ($hour) use ($yesterdayDataRaw) {
    return [$hour => $yesterdayDataRaw->where('hour', (int) substr($hour, 0, 2))->sum('gmv')];
});


        // Top host
        $topMainHostsToday = LivePerformanceSnap::whereIn('room_id', $roomIds)
            ->whereDate('date', $today)
            ->whereNotNull('main_host_id')
            ->groupBy('main_host_id')
            ->selectRaw('main_host_id as staff_id, SUM(gmv) as total_gmv')
            ->orderByDesc('total_gmv')->take(5)->get()
            ->map(fn($h) => [
                'name' => optional(Staff::find($h->staff_id))->name ?? 'Không rõ',
                'gmv' => $h->total_gmv,
            ]);

        $topMainHostsMonth = LivePerformanceSnap::whereIn('room_id', $roomIds)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->whereNotNull('main_host_id')
            ->groupBy('main_host_id')
            ->selectRaw('main_host_id as staff_id, SUM(gmv) as total_gmv')
            ->orderByDesc('total_gmv')->take(5)->get()
            ->map(fn($h) => [
                'name' => optional(Staff::find($h->staff_id))->name ?? 'Không rõ',
                'gmv' => $h->total_gmv,
            ]);

        // GMV theo room (kênh)
        $gmvByRoomToday = Room::when($selectedProjectId, fn($q) => $q->where('project_id', $selectedProjectId))
            ->get()
            ->map(function ($room) use ($today) {
                return [
                    'room' => $room->name,
                    'gmv' => LivePerformanceDay::where('room_id', $room->id)->whereDate('date', $today)->sum('gmv'),
                ];
            })->sortByDesc('gmv')->values();

        $gmvByRoomMonth = Room::when($selectedProjectId, fn($q) => $q->where('project_id', $selectedProjectId))
            ->get()
            ->map(function ($room) use ($startOfMonth, $endOfMonth) {
                return [
                    'room' => $room->name,
                    'gmv' => LivePerformanceDay::where('room_id', $room->id)->whereBetween('date', [$startOfMonth, $endOfMonth])->sum('gmv'),
                ];
            })->sortByDesc('gmv')->values();

        // GMV theo project
        $gmvByProjectToday = Project::with('rooms')->get()->map(function ($project) use ($today) {
            $roomIds = $project->rooms->pluck('id');
            $gmv = LivePerformanceDay::whereIn('room_id', $roomIds)->whereDate('date', $today)->sum('gmv');
            return ['project' => $project->name, 'gmv' => $gmv];
        })->sortByDesc('gmv')->values();

        $gmvByProjectMonth = Project::with('rooms')->get()->map(function ($project) use ($startOfMonth, $endOfMonth) {
            $roomIds = $project->rooms->pluck('id');
            $gmv = LivePerformanceDay::whereIn('room_id', $roomIds)->whereBetween('date', [$startOfMonth, $endOfMonth])->sum('gmv');
            return ['project' => $project->name, 'gmv' => $gmv];
        })->sortByDesc('gmv')->values();


        // === 5. Chi phí theo Room (kênh) ===
$costByRoomToday = Room::query()
    ->when($selectedProjectId, fn($q) => $q->where('project_id', $selectedProjectId))
    ->get()
    ->map(function ($room) use ($today) {
        $cost = LivePerformanceDay::where('room_id', $room->id)
            ->whereDate('date', $today)
            ->sum('ads_total_cost');
        return ['room' => $room->name, 'cost' => $cost];
    })->sortByDesc('cost')->values();

$costByRoomMonth = Room::query()
    ->when($selectedProjectId, fn($q) => $q->where('project_id', $selectedProjectId))
    ->get()
    ->map(function ($room) use ($startOfMonth, $endOfMonth) {
        $cost = LivePerformanceDay::where('room_id', $room->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('ads_total_cost');
        return ['room' => $room->name, 'cost' => $cost];
    })->sortByDesc('cost')->values();

// === 6. Chi phí theo Project (dự án) ===
$costByProjectToday = Project::with('rooms')->get()->map(function ($project) use ($today) {
    $roomIds = $project->rooms->pluck('id');
    $cost = LivePerformanceDay::whereIn('room_id', $roomIds)
        ->whereDate('date', $today)
        ->sum('ads_total_cost');
    return ['project' => $project->name, 'cost' => $cost];
})->sortByDesc('cost')->values();

$costByProjectMonth = Project::with('rooms')->get()->map(function ($project) use ($startOfMonth, $endOfMonth) {
    $roomIds = $project->rooms->pluck('id');
    $cost = LivePerformanceDay::whereIn('room_id', $roomIds)
        ->whereBetween('date', [$startOfMonth, $endOfMonth])
        ->sum('ads_total_cost');
    return ['project' => $project->name, 'cost' => $cost];
})->sortByDesc('cost')->values();


// 5. Kênh có chi phí ads > 8%
$overspendRoomsToday = Room::with('project')->get()->filter(function ($room) use ($today) {
    $gmv = LivePerformanceDay::where('room_id', $room->id)->whereDate('date', $today)->sum('gmv');
    $cost = LivePerformanceDay::where('room_id', $room->id)->whereDate('date', $today)->sum('ads_total_cost');
    return $gmv > 0 && ($cost / $gmv) * 100 > 8;
})->map(function ($room) use ($today) {
    $gmv = LivePerformanceDay::where('room_id', $room->id)->whereDate('date', $today)->sum('gmv');
    $cost = LivePerformanceDay::where('room_id', $room->id)->whereDate('date', $today)->sum('ads_total_cost');
    return [
        'room' => $room->name,
        'project' => optional($room->project)->name,
        'cost_percent' => round(($cost / $gmv) * 100, 2),
    ];
});

$overspendRoomsMonth = Room::with('project')->get()->filter(function ($room) use ($startOfMonth, $endOfMonth) {
    $gmv = LivePerformanceDay::where('room_id', $room->id)->whereBetween('date', [$startOfMonth, $endOfMonth])->sum('gmv');
    $cost = LivePerformanceDay::where('room_id', $room->id)->whereBetween('date', [$startOfMonth, $endOfMonth])->sum('ads_total_cost');
    return $gmv > 0 && ($cost / $gmv) * 100 > 8;
})->map(function ($room) use ($startOfMonth, $endOfMonth) {
    $gmv = LivePerformanceDay::where('room_id', $room->id)->whereBetween('date', [$startOfMonth, $endOfMonth])->sum('gmv');
    $cost = LivePerformanceDay::where('room_id', $room->id)->whereBetween('date', [$startOfMonth, $endOfMonth])->sum('ads_total_cost');
    return [
        'room' => $room->name,
        'project' => optional($room->project)->name,
        'cost_percent' => round(($cost / $gmv) * 100, 2),
    ];
});


       return view('admin.dashboard', compact(
    'projects', 'rooms',
    'selectedProjectId', 'selectedRoomId',
    'todayRevenue', 'yesterdayRevenue',
    'todayCostPercent', 'monthRevenue', 'monthCostPercent',
    'todayTargetPercent', 'monthTargetPercent',
    'todayData', 'yesterdayData', 'allHours',
    'topMainHostsToday', 'topMainHostsMonth',
    'gmvByProjectToday', 'gmvByProjectMonth',
    'gmvByRoomToday', 'gmvByRoomMonth',
    'costByRoomToday', 'costByRoomMonth',
    'costByProjectToday', 'costByProjectMonth',
    'overspendRoomsToday', 'overspendRoomsMonth'

));

    }
}
