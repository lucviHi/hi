<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StreamerDataDay;
use App\Models\Room;
use Shuchkin\SimpleXLSX;
use Carbon\Carbon;

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
        $hour = $type === 'hourly' ? $request->input('hour') : null;
        $route = $type === 'hourly' ? 'live_performance.hourly' : 'live_performance.daily';
        
        if ($xlsx = \Shuchkin\SimpleXLSX::parse($file->getPathname())) {
            $rows = $xlsx->rows();
            array_shift($rows); // Xóa dòng ngày
            array_shift($rows); // Dòng trống
            $header = array_shift($rows); // Dòng tiêu đề

            $expectedColumns = 23;
            $datesToUpdate = []; // chứa các ngày xuất hiện trong file

            foreach ($rows as $row) {
        
                if (count($row) !== $expectedColumns || empty($row[0]))
                    continue;

                try {
                    $start_time = Carbon::createFromFormat('Y-m-d H:i', trim($row[1]));
                    if ($type === 'hourly') {
                   $start_date = $start_time->toDateString();
                   $today = Carbon::today()->toDateString();
                   if ($start_date !== $today) continue;
                      }
                    $date = $start_time->format('Y-m-d'); 
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

                } catch (\Exception $e) {
                    continue;
                }
             
                $datesToUpdate[$date] = true; // lưu lại ngày đang xử lý

                $existing = StreamerDataDay::where('room_id', $room_id)
                    ->where('live_name', $row[0])
                    ->where('start_time', $start_time)
                    ->first();

                $oldData = null;
                if ($existing) {
                    $oldData = [
                        'gmv' => $existing->gmv ?? 0,
                        'paid_orders' => $existing->paid_orders ?? 0,
                        'views' => $existing->views ?? 0,
                        'product_clicks' => $existing->product_clicks ?? 0,
                        'gmv_per_1k_impressions' => $existing->gmv_per_1k_impressions ?? 0,
                    ];
                }

                // Lưu vào bảng chi tiết
                StreamerDataDay::updateOrCreate([
                    'room_id' => $room_id,
                    'live_name' => $row[0],
                    'start_time' => $start_time,
                    'hour' => $type === 'hourly' ? intval($hour) : null,

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
                    'product_clicks' => $row[20] ?? 0,
                    'ctr' => $ctr,
                    'ctor' => $ctor,
                ]);

                // Gọi cập nhật bảng tổng
                // \App\Services\LivePerformanceAggregator::updateFromStreamer(
                //     $room_id,
                //     $date,
                //     $hour,
                //     $type,
                //     $gmv,
                //     $paid_orders,
                //     $views,
                //     $gmv_per_1k_impressions,
                //     $product_clicks,
                //     $oldData // truyền vào đây

                // );
            }
// ✅ Gọi tổng hợp chỉ số sau khi import xong
foreach (array_keys($datesToUpdate) as $dateToUpdate) {
    \App\Services\LivePerformanceAggregator::updateStreamerSummary(
        $room_id,
        $dateToUpdate,
        $type === 'hourly' ? $hour : null,
        $type
    );
}


            return redirect()->route($route, $room_id)
                ->with('success', 'Import dữ liệu thành công!');
        }

        return redirect()->route( $route, $room_id)
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
