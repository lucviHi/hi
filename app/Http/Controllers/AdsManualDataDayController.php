<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdsManualDataDay;
use App\Models\Room;
use Shuchkin\SimpleXLSX;
use Carbon\Carbon;
use Laravel\Pail\ValueObjects\Origin\Console;
use Symfony\Component\Console\Logger\ConsoleLogger;
use App\Services\LivePerformanceAggregator;

class AdsManualDataDayController extends Controller
{
    public function index(Request $request, $room_id)
    {
        $data = AdsManualDataDay::with('room')->where('room_id', $room_id)->paginate(10);

        $room = Room::findOrFail($room_id);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $query = AdsManualDataDay::where('room_id', $room_id);
        $data = AdsManualDataDay::where('room_id', $room_id)->when($startDate, function ($query) use ($startDate) {
            return $query->where('date', '>=', $startDate);
        })
            ->when($endDate, function ($query) use ($endDate) {
                return $query->where('date', '<=', $endDate);
            })
            ->paginate(10)
            ->appends([
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        $data = $query->when($startDate, function ($query) use ($startDate) {
            return $query->where('date', '>=', $startDate);
        })
            ->when($endDate, function ($query) use ($endDate) {
                return $query->where('date', '<=', $endDate);
            })
            ->paginate(10)
            ->appends([
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

        return view('ads_manual_data_days.index', compact('data', 'room', 'startDate', 'endDate'));
    }
    public function import_index($room_id)
    {
        $data = AdsManualDataDay::with(relations: 'room')->get();
        return view('ads_manual_data_days.import_index', compact('room_id')); // Trả về view hiển thị danh sách
    }

    public function import(Request $request, $room_id)
    {

        $request->validate([
            'file' => 'required|mimes:xlsx|max:2048',
        ]);

        $file = $request->file('file');
        $type = $request->input('type', 'daily'); // Mặc định daily
        $route = $type === 'hourly' ? 'live_performance.hourly' : 'live_performance.daily';

        if ($xlsx = SimpleXLSX::parse($file->getPathname())) {
            $rows = $xlsx->rows();
            $header = array_shift($rows);
            $expectedColumns = 27;

            foreach ($rows as $row) {
                if (count($row) !== $expectedColumns || empty($row[0])) {
                    continue;
                }

                try {
                    $date = Carbon::createFromFormat('Y-m-d', trim($row[0]));
                } catch (\Exception $e) {
                    continue;
                }

                //$hour = $type === 'hourly' ? $date->format('H:00:00') : null;
                $hour = $type === 'hourly' ? intval($request->input('hour')) : null;

                $dateOnly = $date->toDateString();
                $impressions = $row[7] ?? 0;
                $cpm = $row[9] ?? 0;
                $roas = $row[17] ?? 0; // roas_onsite

                $cost_vnd = ($impressions * $cpm) / 1000;
                $manual_revenue = $cost_vnd * $roas;
                $data = [
                    'cost_usd' => $row[1] ?? 0,
                    'cost_vnd' => $cost_vnd,
                    'cpc_usd' => $row[3] ?? 0,
                    'cpa_usd' => $row[4] ?? 0,
                    'total_purchases' => $row[5] ?? 0,
                    'cost_per_payment' => $row[6] ?? 0,
                    'impressions' => $impressions,
                    'ctr' => $row[8] ?? 0,
                    'cpm' => $cpm,
                    'cpc' => $row[10] ?? 0,
                    'clicks' => $row[11] ?? 0,
                    'conversions' => $row[12] ?? 0,
                    'cvr' => $row[13] ?? 0,
                    'cpa' => $row[14] ?? 0,
                    'roas_purchase' => $row[15] ?? 0,
                    'roas_payment' => $roas,
                    'roas_on_site' => $row[17] ?? 0,
                    'shopping_purchases' => $row[18] ?? 0,
                    'purchase_count' => $row[19] ?? 0,
                    'cost_per_purchase' => $row[20] ?? 0,
                    'cost_per_shopping_purchase' => $row[21] ?? 0,
                    'total_payments' => $row[22] ?? 0,
                    'cost_per_payment_repeat' => $row[23] ?? 0,
                    'video_views' => $row[24] ?? 0,
                    'video_views_2s' => $row[25] ?? 0,
                    'video_views_6s' => $row[26] ?? 0,
                    'manual_revenue' => $manual_revenue,
                    'type' => $type,
                    'hour' => $hour,
                ];

                AdsManualDataDay::updateOrCreate(
                    [
                        'room_id' => $room_id,
                        'date' => $dateOnly,
                        'hour' => $hour,
                        'type' => $type,
                    ],
                    $data
                );

                // 👉 TODO: gọi hàm cập nhật bảng tổng sau khi insert/update
                LivePerformanceAggregator::updateFromManual(
                    $room_id,
                    $dateOnly,
                    $hour,
                    $type,
                    $data['cost_vnd'],
                    $data['manual_revenue'],
                    $data['roas_payment'] ?? 0
                );
            }

            // ✅ Sau khi import xong → gọi snapshot delta
            if ($type === 'hourly') {
                app(\App\Http\Controllers\LivePerformanceSnapController::class)
                    ->snapshotDeltaHourly($room_id, $dateOnly);
            }

            app(\App\Http\Controllers\LivePerformanceSnapController::class)
                ->snapshotDeltaDaily($room_id, $dateOnly);

            return redirect()->route($route, $room_id)
                ->with('success', 'Import dữ liệu thành công!');
        } else {
            return redirect()->route($route, $room_id)
                ->with('error', 'Lỗi khi đọc file Excel.');
        }
    }


    public function destroy(Request $request, $id)
    {
        // Tìm và xóa bản ghi
        $data = AdsManualDataDay::findOrFail($id);
        $data->delete();

        // Lấy room_id từ request
        $room_id = $request->input('room_id');

        // Kiểm tra nếu room_id không tồn tại
        if (!$room_id) {
            return redirect()->back()->with('error', 'Thiếu tham số room_id.');
        }

        // Lấy các tham số truy vấn hiện tại
        $queryParams = $request->only(['start_date', 'end_date']);

        // Thêm room_id vào mảng tham số
        $queryParams['room_id'] = $room_id;

        // Chuyển hướng về route 'ads_manual_data_days.index' với các tham số truy vấn
        return redirect()->route('ads_manual_data_days.index', $queryParams)
            ->with('success', 'Bản ghi đã được xóa thành công.');
    }


    // Phương thức hiển thị danh sách các bản ghi đã bị xóa mềm
    public function trashed($room_id)
    {
        $trashedData = AdsManualDataDay::onlyTrashed()
            ->where('room_id', $room_id)
            ->paginate(10);

        return view('ads_manual_data_days.trashed', compact('trashedData', 'room_id'));
    }

    // Phương thức khôi phục một bản ghi đã bị xóa mềm
    public function restore(Request $request, $id)
    {

        $data = AdsManualDataDay::withTrashed()->findOrFail($id);
        $data->restore();
        $room_id = $data->room_id;
        return redirect()->route('ads_manual_data_days.trashed', ['room_id' => $room_id])
            ->with('success', 'Bản ghi đã được khôi phục thành công.');
    }

    // Phương thức xóa vĩnh viễn một bản ghi
    public function forceDelete(Request $request, $id)
    {
        $data = AdsManualDataDay::withTrashed()->findOrFail($id);
        $data->forceDelete();
        $room_id = $data->room_id;
        return redirect()->route('ads_manual_data_days.trashed', ['room_id' => $room_id])
            ->with('success', 'Bản ghi đã được xóa vĩnh viễn.');
    }
}
