<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdsGmvMaxDataDay;
use App\Models\Room;
use Shuchkin\SimpleXLSX;
use Carbon\Carbon;

class AdsGmvMaxDataDayController extends Controller
{
    public function index(Request $request, $room_id)
    {
        $data = AdsGmvMaxDataDay::with('room')->where('room_id', $room_id)->paginate(10);
        $room = Room::findOrFail($room_id);
       
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = AdsGmvMaxDataDay::where('room_id', $room_id);
        $data = $query->when($startDate, function ($query) use ($startDate) {
            return $query->where('launch_time', '>=', $startDate);
        })->when($endDate, function ($query) use ($endDate) {
            return $query->where('launch_time', '<=', $endDate);
        })->paginate(10)->appends([
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
        $data->getCollection()->transform(function ($item) {
            $item->launch_time = \Carbon\Carbon::parse($item->launch_time)->format('Y-m-d');
            return $item;
        });
    
        return view('ads_gmv_max_data_days.index', compact('data', 'room', 'startDate', 'endDate'));
    }

    public function import_index($room_id)
    {
        return view('ads_gmv_max_data_days.import_index', compact('room_id'));
    }

public function import(Request $request, $room_id)
{
    $request->validate([
        'file' => 'required|mimes:xlsx|max:2048',
    ]);

    $file = $request->file('file');

    if ($xlsx = SimpleXLSX::parse($file->getPathname())) {
        $rows = $xlsx->rows();
        $header = array_shift($rows); // Bỏ qua header nếu có

        $expectedColumns = 20; // Số cột mong đợi

        // Kiểm tra số cột
        foreach ($rows as $index => $row) {
            if (count($row) !== $expectedColumns) {
                return redirect()->route('ads_gmv_max_data_days.index', $room_id)
                    ->with('error', "Không đúng số cột. Vui lòng kiểm tra lại file.");
            }
        }

        foreach ($rows as $row) {
            if (empty($row[0])|| empty($row[4])) {
                continue;
            }

            try {
                $launch_time = Carbon::createFromFormat('Y-m-d H:i:s', trim($row[2]))->format('Y-m-d');
            } catch (\Exception $e) {
                continue;
            }

            // Kiểm tra xem dữ liệu đã tồn tại chưa
            $exists = AdsGmvMaxDataDay::where('room_id', $room_id)
                ->where('live_session_name', $row[0])
                ->where('launch_time', $launch_time)
                ->exists();

            if ($exists) {
                continue; // Bỏ qua nếu dữ liệu đã tồn tại
            }

            AdsGmvMaxDataDay::create([
                'room_id' => $room_id,
                'live_session_name' => $row[0] ?? null,
                'status' => $row[1] ?? null,
                'launch_time' => $launch_time ?? null,
                'duration' => $row[3] ?? 0,
                'cost' => $row[4] ?? 0,
                'net_cost' => $row[5] ?? 0,
                'sku_orders' => $row[6] ?? 0,
                'cost_per_order' => $row[7] ?? 0,
                'gross_revenue' => $row[8] ?? 0,
                'roi' => $row[9] ?? 0,
                'live_views' => $row[10] ?? 0,
                'cost_per_view' => $row[11] ?? 0,
                'ten_sec_views' => $row[12] ?? 0,
                'cost_per_ten_sec_view' => $row[13] ?? 0,
                'followers' => $row[14] ?? 0,
                'store_orders' => $row[15] ?? 0,
                'cost_per_store_order' => $row[16] ?? 0,
                'gross_revenue_store' => $row[17] ?? 0,
                'roi_store' => $row[18] ?? 0,
                'currency' => $row[19] ?? null,
            ]);
        }

        return redirect()->route('ads_gmv_max_data_days.index', $room_id)->with('success', 'Import dữ liệu thành công!');
    } else {
        return redirect()->route('ads_gmv_max_data_days.index', $room_id)->with('error', 'Lỗi khi đọc file Excel.');
    }
}

    public function destroy(Request $request, $id)
    {
        $data = AdsGmvMaxDataDay::findOrFail($id);
        $data->delete();

        $room_id = $request->input('room_id');
        if (!$room_id) {
            return redirect()->back()->with('error', 'Thiếu tham số room_id.');
        }

        return redirect()->route('ads_gmv_max_data_days.index', ['room_id' => $room_id])
                         ->with('success', 'Bản ghi đã được xóa thành công.');
    }

    public function trashed($room_id)
    {
        $trashedData = AdsGmvMaxDataDay::onlyTrashed()
            ->where('room_id', $room_id)
            ->paginate(10);

        return view('ads_gmv_max_data_days.trashed', compact('trashedData', 'room_id'));
    }

    public function restore(Request $request, $id)
    {
        $data = AdsGmvMaxDataDay::withTrashed()->findOrFail($id);
        $data->restore();
        $room_id = $data->room_id;

        return redirect()->route('ads_gmv_max_data_days.trashed', ['room_id' => $room_id])
                         ->with('success', 'Bản ghi đã được khôi phục thành công.');
    }

    public function forceDelete(Request $request, $id)
    {
        $data = AdsGmvMaxDataDay::withTrashed()->findOrFail($id);
        $data->forceDelete();
        $room_id = $data->room_id;

        return redirect()->route('ads_gmv_max_data_days.trashed', ['room_id' => $room_id])
                         ->with('success', 'Bản ghi đã được xóa vĩnh viễn.');
    }
    
}
