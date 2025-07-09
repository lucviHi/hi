<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LivePerformanceDay;
use App\Models\LiveTargetDay;
use App\Models\LivePerformanceSnap;
use App\Models\Staff;
use App\Models\Room;
use App\Services\LivePerformanceSnapService;
use Carbon\Carbon;

class LivePerformanceSnapController extends Controller
{
    // Gọi snapshot theo type: hourly hoặc daily
    public function snapshotDelta($room_id, $date, $type = 'hourly')
    {
        if ($type === 'daily') {
            $this->snapshotDeltaDaily($room_id, $date);
        } elseif ($type === 'hourly') {
            $this->snapshotDeltaHourly($room_id, $date);
        }

        return response()->json([
            'message' => "Đã tạo snapshot delta [$type] cho phòng $room_id ngày $date"
        ]);
    }

    // Snapshot chênh lệch theo ngày (hôm nay - hôm qua)
    public function snapshotDeltaDaily($room_id, $date)
    {
        $prevDate = Carbon::parse($date)->subDay()->toDateString();

        $curr = LivePerformanceDay::where('room_id', $room_id)
            ->where('type', 'daily')->where('date', $date)->first();

        $prev = LivePerformanceDay::where('room_id', $room_id)
            ->where('type', 'daily')->where('date', $prevDate)->first();

        if ($curr && $prev) {
            $this->storeDeltaSnapshot($room_id, $date, null, 'daily', $curr, $prev);
        }
    }

    // Snapshot chênh lệch theo giờ (giờ N - N-1)
    // public function snapshotDeltaHourly($room_id, $date)
    // {
    //     $data = LivePerformanceDay::where('room_id', $room_id)
    //         ->where('type', 'hourly')->where('date', $date)
    //         ->orderBy('hour')->get();

    //     for ($i = 1; $i < $data->count(); $i++) {
    //         $curr = $data[$i];
    //         $prev = $data[$i - 1];

    //         $this->storeDeltaSnapshot($room_id, $date, $curr->hour, 'hourly', $curr, $prev);
    //     }
    // }
public function snapshotDeltaHourly($room_id, $date)
{
    $data = LivePerformanceDay::where('room_id', $room_id)
        ->where('type', 'hourly')
        ->where('date', $date)
        ->orderBy('hour')
        ->get();

    if ($data->count() > 0) {
        // Snapshot đầu tiên (delta với 0)
        $curr = $data[0];
        $prev = clone $curr;

        $prev->gmv = 0;
        $prev->ads_total_cost = 0;
        $prev->views = 0;
        $prev->live_impressions = 0;
        $prev->items_sold = 0;
        $prev->product_clicks = 0;
        $prev->comments = 0;
        $prev->shares = 0;

        $this->storeDeltaSnapshot($room_id, $date, $curr->hour, 'hourly', $curr, $prev);
    }

    // Các snapshot tiếp theo (delta thực)
    for ($i = 1; $i < $data->count(); $i++) {
        $curr = $data[$i];
        $prev = $data[$i - 1];

        $this->storeDeltaSnapshot($room_id, $date, $curr->hour, 'hourly', $curr, $prev);
    }
}

    // Hàm tạo snapshot delta
    public function storeDeltaSnapshot($room_id, $date, $hour, $type, $curr, $prev)
    {
        $views = $curr->views - $prev->views;
        $clicks = $curr->product_clicks - $prev->product_clicks;
        $items = $curr->items_sold - $prev->items_sold;
        $impressions = $curr->live_impressions - $prev->live_impressions;

        LivePerformanceSnapService::snapshot([
            'room_id' => $room_id,
            'date' => $date,
            'hour' => $hour,
            'type' => $type,
            'gmv' => $curr->gmv - $prev->gmv,
            'ads_total_cost' => $curr->ads_total_cost - $prev->ads_total_cost,
            'views' => $views,
            'live_impressions' => $impressions,
            'items_sold' => $items,
            'product_clicks' => $clicks,
            'comments' => $curr->comments - $prev->comments,
            'shares' => $curr->shares - $prev->shares,
            'entry_rate' => $impressions > 0 ? round($views*100 / $impressions, 4) : null,
            'ctr' => $views > 0 ? round($clicks*100 / $views, 4) : null,
            'ctor' => $clicks > 0 ? round($items*100 / $clicks, 4) : null,
        ]);
    }

    // So sánh snapshot delta theo giờ (dùng cho báo cáo)
public function compareHourlyFromSnapshot(Request $request, $room_id)
    {
       $staffs = Room::findOrFail($room_id)->staffs()->orderBy('name')->get();

        $date = $request->input('date', now()->toDateString());
        $hourFrom = (int) $request->input('hour_from', 0);
        $hourTo = (int) $request->input('hour_to', 23);

        $target = LiveTargetDay::where('room_id', $room_id)
            ->where('date', $date)
            ->first();

        $teamCount = $target->team_count ?? 0;
        $gmvTarget = $target->gmv_target ?? 0;
        $totalHours = $teamCount * 4;
        $targetPerHour = ($totalHours > 0 && $gmvTarget > 0) ? $gmvTarget / $totalHours : 0;

        $snapshots = LivePerformanceSnap::where('room_id', $room_id)
            ->where('type', 'hourly')
            ->where('date', $date)
            ->whereBetween('hour', [$hourFrom, $hourTo])
            ->orderBy('hour')
            ->get();
            //Lọc theo nhân viên 
        if ($request->filled('staff_id')) {
           $staffId = $request->input('staff_id');
           $snapshots = $snapshots->filter(function ($snap) use ($staffId) {
        return $snap->main_host_id == $staffId || $snap->support_host_id == $staffId;
    })->values(); // reset lại key sau khi filter
}
        $differences = $snapshots->map(function ($snap) use ($targetPerHour) {
            return (object) [
                ...$snap->toArray(),
                'target_gmv' => $targetPerHour,
                'percent_achieved' => $targetPerHour > 0
                    ? round($snap->gmv / $targetPerHour * 100, 2)
                    : null,
            ];
        });

return view('room_report.report_hourly', compact(
    'differences', 'room_id', 'date', 'hourFrom', 'hourTo', 'targetPerHour', 'staffs'
))->with('staff_id', $request->input('staff_id'));

    }


    public function assignHosts(Request $request)
{
    $data = $request->validate([
        'room_id' => 'required|integer',
        'date' => 'required|date',
        'hour' => 'nullable|integer',
        'main_host_id' => 'nullable|exists:staffs,id',
        'support_host_id' => 'nullable|exists:staffs,id',
    ]);

    $snap = \App\Models\LivePerformanceSnap::where([
        'room_id' => $data['room_id'],
        'date' => $data['date'],
        'hour' => $data['hour'],
        'type' => 'hourly',
    ])->first();

    if ($snap) {
        $snap->main_host_id = $data['main_host_id'];
        $snap->support_host_id = $data['support_host_id'];
        $snap->save();
    }

    return back()->with('success', 'Đã lưu thông tin host.');
}


}
