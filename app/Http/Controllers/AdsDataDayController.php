<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdsDataDay;
use App\Models\AdsGmvMaxDataDay;
use App\Models\AdsManualDataDay;
use App\Models\Room;
use Carbon\Carbon;
use DB;

class AdsDataDayController extends Controller
{
    /**
     * Hiển thị danh sách dữ liệu theo room_id, có lọc theo thời gian.
     */
    public function index(Request $request)
    {
        $rooms = Room::all(); // Lấy danh sách tất cả các phòng
        $roomId = $request->input('room_id', $rooms->first()->id ?? null);
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $adsData = AdsDataDay::where('room_id', $roomId)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'asc')
            ->get();

        return view('ads_data_days.index', compact('adsData', 'rooms', 'roomId', 'startDate', 'endDate'));
    }

    /**
     * Cập nhật toàn bộ dữ liệu tổng hợp từ AdsManualDataDay và AdsGmvMaxDataDay vào bảng AdsDataDay.
     */
    public function updateData()
    {
        $rooms = Room::all();
        $dates = AdsManualDataDay::select('date')->union(query: AdsGmvMaxDataDay::select('date'))->distinct()->get();

        foreach ($rooms as $room) {
            foreach ($dates as $dateObj) {
                $date = $dateObj->date;
                
                // Lấy dữ liệu từ bảng Ads GMV Max
                $gmvMax = AdsGmvMaxDataDay::where('room_id', $room->id)->where('date', $date)->first();
                
                // Lấy dữ liệu từ bảng Ads Manual
                $manual = AdsManualDataDay::where('room_id', $room->id)->where('date', $date)->first();
                
                // Tổng hợp dữ liệu
                $data = [
                    'date' => $date,
                    'room_id' => $room->id,
                    'gmv_max_cost' => $gmvMax->cost ?? 0,
                    'gmv_max_gross_revenue' => $gmvMax->gross_revenue ?? 0,
                    'gmv_max_store_revenue' => $gmvMax->store_revenue ?? 0,
                    'gmv_max_real_revenue' => $gmvMax->real_revenue ?? 0,
                    'manual_cost' => $manual->cost ?? 0,
                    'manual_roas' => $manual->roas ?? 0,
                    'manual_revenue' => $manual->revenue ?? 0,
                    'total_ads_cost' => ($gmvMax->cost ?? 0) + ($manual->cost ?? 0),
                    'total_ads_revenue' => ($gmvMax->real_revenue ?? 0) + ($manual->revenue ?? 0),
                    'total_roas' => ($manual->roas ?? 0),
                ];

                AdsDataDay::updateOrCreate([
                    'date' => $date,
                    'room_id' => $room->id,
                ], $data);
            }
        }

        return redirect()->route('ads_data_days.index')->with('success', 'Dữ liệu đã được cập nhật thành công!');
    }
}
