<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StreamerDataDay;
use App\Models\Room;
use Shuchkin\SimpleXLSX;
use Carbon\Carbon;
use App\Models\LivePerformanceDay;
class StreamerDataDayController extends Controller
{
    public function index(Request $request, $room_id)
    {
        $data = StreamerDataDay::with('room')->where('room_id', $room_id)->paginate(10);
        $room = Room::findOrFail($room_id);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = StreamerDataDay::where('room_id', $room_id);
        $data = $query->when($startDate, function ($query) use ($startDate) {
            return $query->where('launch_time', '>=', $startDate);
        })->when($endDate, function ($query) use ($endDate) {
            return $query->where('launch_time', '<=', $endDate);
        })->paginate(10)->appends([
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ]);
        $data->getCollection()->transform(function ($item) {
            $item->start_time = \Carbon\Carbon::parse($item->start_time)->format('Y-m-d');
            return $item;
        });

        return view('streamer_data_days.index', compact('data', 'room', 'startDate', 'endDate'));
    }

    public function import_index($room_id)
    {
        return view('streamer_data_days.import_index', compact('room_id'));
    }

    public function import(Request $request, $room_id)
{
    $request->validate([
        'file' => 'required|mimes:xlsx|max:2048',
        'type' => 'required|in:daily,hourly',
        'hour' => 'required_if:type,hourly'
    ]);

    $file = $request->file('file');
    $type = $request->input('type');
    $hour = $type === 'hourly' ? (int)$request->input('hour') : null;
    $route = $type === 'hourly' ? 'live_performance.hourly' : 'live_performance.daily';

    if ($xlsx = \Shuchkin\SimpleXLSX::parse($file->getPathname())) {
        $rows = $xlsx->rows();
        array_shift($rows); // Xóa dòng ngày
        array_shift($rows); // Dòng trống
        $header = array_shift($rows); // Dòng tiêu đề

        $expectedColumns = 23;
        $datesToUpdate = []; // chứa các ngày xuất hiện trong file
        $cleared = [];
        foreach ($rows as $row) {
            if (count($row) !== $expectedColumns || empty($row[0])) continue;

            try {
                $start_time = Carbon::createFromFormat('Y-m-d H:i', trim($row[1]));

                if ($type === 'hourly') {
                    $start_date = $start_time->toDateString();
                    $today = Carbon::today()->toDateString();
                    if ($start_date !== $today) continue;
                }

                $date = $start_time->format('Y-m-d');
                $datesToUpdate[$date] = true;

                // Parse các chỉ số
                $gmv = (int) str_replace(['₫', '.', ','], '', $row[4]);
                $total_revenue = (int) str_replace(['₫', '.', ','], '', $row[3]);
                $avg_price = (int) str_replace(['₫', '.', ','], '', $row[7]);
                $gmv_per_1k_impressions = (int) str_replace(['₫', '.', ','], '', $row[9]);
                $gmv_per_1k_views = (int) str_replace(['₫', '.', ','], '', $row[10]);
                $paid_orders = (int) $row[8];
                $views = (int) $row[11];
                $ctr = (float) $row[21] ?? null;
                $ctor = (float) $row[22] ?? null;
                $product_clicks = (int) $row[20];

                $key = $room_id . '-' . $date . '-' . $hour . '-' . $type;

if (!isset($cleared[$key])) {
    StreamerDataDay::where('room_id', $room_id)
        ->whereDate('start_time', $date)
        ->when($type === 'hourly', fn($q) => $q->where('hour', $hour))
        ->where('type', $type)
        ->delete();

    $cleared[$key] = true;
}


                // Lưu vào bảng chi tiết
                StreamerDataDay::Create([
                    'room_id' => $room_id,
                    'live_name' => $row[0],
                    'start_time' => $start_time,
                    'hour' => $type === 'hourly' ? $hour : null,
                    'type' => $type,
                    'duration' => abs((int) $row[2]),
                    'total_revenue' => $total_revenue,
                    'gmv' => $gmv,
                    'items_sold' => $row[5] ?? 0,
                    'customers' => $row[6] ?? 0,
                    'avg_price' => $avg_price,
                    'paid_orders' => $paid_orders,
                    'gmv_per_1k_impressions' => $gmv_per_1k_impressions,
                    'gmv_per_1k_views' => $gmv_per_1k_views,
                    'views' => $views,
                    'viewers' => $row[12] ?? 0,
                    'max_viewers' => $row[13] ?? 0,
                    'new_followers' => $row[14] ?? 0,
                    'avg_watch_time' => $row[15] ?? 0,
                    'likes' => $row[16] ?? 0,
                    'comments' => $row[17] ?? 0,
                    'shares' => $row[18] ?? 0,
                    'product_displays' => $row[19] ?? 0,
                    'product_clicks' => $product_clicks,
                    'ctr' => $ctr,
                    'ctor' => $ctor,
                ]);
            } catch (\Exception $e) {
                continue;
            }
        }

        // ✅ Gọi tổng hợp lại sau khi import
        foreach (array_keys($datesToUpdate) as $dateToUpdate) {
            // Xóa bản tổng hợp cũ trước khi tổng lại
            LivePerformanceDay::where([
                'room_id' => $room_id,
                'date' => $dateToUpdate,
                'hour' => $type === 'hourly' ? $hour : null,
                'type' => $type
            ])->delete();

            \App\Services\LivePerformanceAggregator::updateStreamerSummary(
                $room_id,
                $dateToUpdate,
                $type === 'hourly' ? $hour : null,
                $type
            );

            if ($type === 'hourly') {
                app(\App\Http\Controllers\LivePerformanceSnapController::class)
                    ->snapshotDeltaHourly($room_id, $dateToUpdate);
            }

            app(\App\Http\Controllers\LivePerformanceSnapController::class)
                ->snapshotDeltaDaily($room_id, $dateToUpdate);
        }

        return redirect()->route($route, $room_id)
            ->with('success', 'Import dữ liệu thành công!');
    }

    return redirect()->route($route, $room_id)
        ->with('error', 'Lỗi khi đọc file Excel.');
}


    public function destroy(Request $request, $id)
    {
        $data = StreamerDataDay::findOrFail($id);
        $data->delete();

        $room_id = $request->input('room_id');
        if (!$room_id) {
            return redirect()->back()->with('error', 'Thiếu tham số room_id.');
        }

        return redirect()->route('streamer_data_days.index', ['room_id' => $room_id])
            ->with('success', 'Bản ghi đã được xóa thành công.');
    }

    public function trashed($room_id)
    {
        $trashedData = StreamerDataDay::onlyTrashed()
            ->where('room_id', $room_id)
            ->paginate(10);

        return view('streamer_data_days.trashed', compact('trashedData', 'room_id'));
    }

    public function restore(Request $request, $id)
    {
        $data = StreamerDataDay::withTrashed()->findOrFail($id);
        $data->restore();
        $room_id = $data->room_id;

        return redirect()->route('streamer_data_days.trashed', ['room_id' => $room_id])
            ->with('success', 'Bản ghi đã được khôi phục thành công.');
    }

    public function forceDelete(Request $request, $id)
    {
        $data = StreamerDataDay::withTrashed()->findOrFail($id);
        $data->forceDelete();
        $room_id = $data->room_id;

        return redirect()->route('streamer_data_days.trashed', ['room_id' => $room_id])
            ->with('success', 'Bản ghi đã được xóa vĩnh viễn.');
    }

}
