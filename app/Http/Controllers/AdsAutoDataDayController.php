<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdsAutoDay;
use App\Models\Room;
use Shuchkin\SimpleXLSX;
use Carbon\Carbon;
use App\Services\LivePerformanceAggregator;

class AdsAutoDataDayController extends Controller
{
    public function index(Request $request, $room_id)
    {
        $room = Room::findOrFail($room_id);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $type = $request->input('type', 'daily');

        $query = AdsAutoDay::where('room_id', $room_id)
            ->where('type', $type);

        if ($startDate) {
            $query->where('date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('date', '<=', $endDate);
        }

        $data = $query->orderByDesc('date')->paginate(10)->appends(compact('startDate', 'endDate', 'type'));

        return view('ads_auto_data_days.index', compact('data', 'room', 'startDate', 'endDate', 'type'));
    }

    // public function import_index($room_id)
    // {
    //     return view('ads_auto_data_days.import_index', compact('room_id'));
    // }

    // public function import(Request $request, $room_id)
    // {
    //     $request->validate([
    //         'file' => 'required|mimes:xlsx|max:2048',
    //         'date' => 'required|date',
    //         'type' => 'required|in:daily,hourly',
    //         'hour' => 'required_if:type,hourly'
    //     ]);
    
    //     $file = $request->file('file');
    //     $date = $request->input('date');
    //     $type = $request->input('type');
    //     $hour = $type === 'hourly' ? $request->input('hour') : null;
    
    //     if ($xlsx = SimpleXLSX::parse($file->getPathname())) {
    //         $rows = $xlsx->rows();
    //         $header = array_shift($rows); // Bỏ dòng tiêu đề
    
    //         $expectedColumns = 18;
    
    //         foreach ($rows as $index => $row) {
    //             if (count($row) !== $expectedColumns || empty($row[0])) {
    //                 continue;
    //             }
    
    //             $data = [ 
    //                 'campaign_id' => $row[0],
    //                 'campaign_name' => $row[1],
    //                 'cost' => $row[2] ?? 0,
    //                 'net_cost' => $row[3] ?? 0,
    //                 'sku_orders' => $row[4] ?? 0,
    //                 'cost_per_order' => $row[5] ?? 0,
    //                 'gross_revenue' => $row[6] ?? 0,
    //                 'roi' => $row[7] ?? 0,
    //                 'live_views' => $row[8] ?? 0,
    //                 'cost_per_view' => $row[9] ?? 0,
    //                 'ten_sec_views' => $row[10] ?? 0,
    //                 'cost_per_ten_sec_view' => $row[11] ?? 0,
    //                 'followers' => $row[12] ?? 0,
    //                 'store_orders' => $row[13] ?? 0,
    //                 'cost_per_store_order' => $row[14] ?? 0,
    //                 'gross_revenue_store' => $row[15] ?? 0,
    //                 'roi_store' => $row[16] ?? 0,
    //                 'currency' => $row[17] ?? 'VND',
    //                 'type' => $type,
    //                 'hour' => $hour,
    //             ];
    //             $data['room_id'] = $room_id;
    //             $data['date'] = $date;
    
    //             // Cập nhật hoặc tạo mới (chỉ 1 dòng mỗi ngày/giờ)
    //             AdsAutoDay::updateOrCreate(
    //                 [
    //                     'room_id' => $room_id,
    //                     'date' => $date,
    //                     'hour' => $hour,
    //                     'type' => $type,
    //                 ],
    //         $data
    //             );
                
        
    //             // Gọi service cập nhật bảng tổng hợp
    //             LivePerformanceAggregator::updateFromAuto(
    //                 $room_id,
    //                 $date,
    //                 $hour,
    //                 $type,
    //                 $data['cost'],
    //                 $data['gross_revenue'],
    //                 $data['roi']
    //             );
    //         }
    
    //         return redirect()->route('live_performance.daily', $room_id)
    //                          ->with('success', 'Import dữ liệu Ads Auto thành công!');
    //     }
    
    //     return redirect()->route('live_performance.daily', $room_id)
    //                      ->with('error', 'Lỗi khi đọc file Excel.');
    // }
    
    // public function import(Request $request, $room_id)
    // {
    //     $request->validate([
    //         'file' => 'required|mimes:xlsx|max:2048',
    //         'date' => 'required|date',
    //         'type' => 'required|in:daily,hourly',
    //         'hour' => 'required_if:type,hourly'
    //     ]);
    
    //     $file = $request->file('file');
    //     $date = $request->input('date');
    //     $type = $request->input('type');
    //     $hour = $type === 'hourly' ? $request->input('hour') : null;
    
    //     if ($xlsx = SimpleXLSX::parse($file->getPathname())) {
    //         $rows = $xlsx->rows();
    //         array_shift($rows); // Bỏ dòng tiêu đề
    
    //         $expectedColumns = 18;
    
    //         // Tổng các chỉ số
    //         $cost = $netCost = $skuOrders = $grossRevenue = 0;
    //         $liveViews = $tenSecViews = $followers = 0;
    //         $storeOrders = $grossRevenueStore = 0;
    
    //         foreach ($rows as $row) {
    //             if (count($row) !== $expectedColumns || empty($row[0])) continue;
    
    //             $cost += (int) $row[2] ?? 0;
    //             $netCost += (int) $row[3] ?? 0;
    //             $skuOrders += (int) $row[4] ?? 0;
    //             $grossRevenue += (int) $row[6] ?? 0;
    //             $liveViews += (int) $row[8] ?? 0;
    //             $tenSecViews += (int) $row[10] ?? 0;
    //             $followers += (int) $row[12] ?? 0;
    //             $storeOrders += (int) $row[13] ?? 0;
    //             $grossRevenueStore += (int) $row[15] ?? 0;
    //         }
    
    //         // Tính toán trung bình
    //         $costPerOrder = $skuOrders > 0 ? round($cost / $skuOrders) : 0;
    //         $roi = $cost > 0 ? round($grossRevenue / $cost, 2) : 0;
    //         $costPerView = $liveViews > 0 ? round($cost / $liveViews) : 0;
    //         $costPerTenSecView = $tenSecViews > 0 ? round($cost / $tenSecViews) : 0;
    //         $costPerStoreOrder = $storeOrders > 0 ? round($cost / $storeOrders) : 0;
    //         $roiStore = $cost > 0 ? round($grossRevenueStore / $cost, 2) : 0;
    //         $campaignId = crc32("campaign-{$room_id}-{$date}-{$hour}-{$type}");
    //         // ✅ Ghi bản tổng vào bảng chi tiết (ads_auto_data_days)
    //         AdsAutoDay::updateOrCreate([
    //             'room_id' => $room_id,
    //             'date' => $date,
    //             'hour' => $hour,
    //             'type' => $type,
    //             'campaign_id' =>  $campaignId, // đánh dấu bản tổng
    //         ], [
    //             'campaign_name' => 'Tổng hợp',
    //             'cost' => $cost,
    //             'net_cost' => $netCost,
    //             'sku_orders' => $skuOrders,
    //             'cost_per_order' => $costPerOrder,
    //             'gross_revenue' => $grossRevenue,
    //             'roi' => $roi,
    //             'live_views' => $liveViews,
    //             'cost_per_view' => $costPerView,
    //             'ten_sec_views' => $tenSecViews,
    //             'cost_per_ten_sec_view' => $costPerTenSecView,
    //             'followers' => $followers,
    //             'store_orders' => $storeOrders,
    //             'cost_per_store_order' => $costPerStoreOrder,
    //             'gross_revenue_store' => $grossRevenueStore,
    //             'roi_store' => $roiStore,
    //             'currency' => 'VND',
    //         ]);
    
    //         // ✅ Ghi vào bảng tổng live_performance_days
    //         \App\Services\LivePerformanceAggregator::updateFromAuto(
    //             $room_id,
    //             $date,
    //             $hour,
    //             $type,
    //             $cost,
    //             $grossRevenue,
    //             $roi,
          
    //         );
    
    //         return redirect()->route('live_performance.daily', $room_id)
    //                          ->with('success', 'Import dữ liệu Ads Auto thành công!');
    //     }
    
    //     return redirect()->route('live_performance.daily', $room_id)
    //                      ->with('error', 'Không thể đọc file Excel.');
    // }
    
    public function import(Request $request, $room_id)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx|max:2048',
            'date' => 'required|date',
            'type' => 'required|in:daily,hourly',
            'hour' => 'required_if:type,hourly'
        ]);
    
        $file = $request->file('file');
        $date = $request->input('date');
        $type = $request->input('type');
        $hour = $type === 'hourly' ? $request->input('hour') : null;
    
        if ($xlsx = \Shuchkin\SimpleXLSX::parse($file->getPathname())) {
            $rows = $xlsx->rows();
            array_shift($rows); // Bỏ header
    
            $expectedColumns = 18;
    
            // Tổng
            $cost = $grossRevenue = $roiSum = 0;
            $count = 0;
    
            foreach ($rows as $row) {
                if (count($row) !== $expectedColumns || empty($row[2])) continue;
    
                // Ghi từng chiến dịch
                \App\Models\AdsAutoDay::updateOrCreate([
                    'room_id' => $room_id,
                    'date' => $date,
                    'hour' => $hour,
                    'type' => $type,
                    'campaign_id' => $row[0],
                ], [
                    'campaign_name' => $row[1],
                    'cost' => $row[2] ?? 0,
                    'net_cost' => $row[3] ?? 0,
                    'sku_orders' => $row[4] ?? 0,
                    'cost_per_order' => $row[5] ?? 0,
                    'gross_revenue' => $row[6] ?? 0,
                    'roi' => $row[7] ?? 0,
                    'live_views' => $row[8] ?? 0,
                    'cost_per_view' => $row[9] ?? 0,
                    'ten_sec_views' => $row[10] ?? 0,
                    'cost_per_ten_sec_view' => $row[11] ?? 0,
                    'followers' => $row[12] ?? 0,
                    'store_orders' => $row[13] ?? 0,
                    'cost_per_store_order' => $row[14] ?? 0,
                    'gross_revenue_store' => $row[15] ?? 0,
                    'roi_store' => $row[16] ?? 0,
                    'currency' => $row[17] ?? 'VND',
                ]);
    
                // Cộng tổng
                $cost += (int) $row[2] ?? 0;
                $grossRevenue += (int) $row[6] ?? 0;
                $roiSum += (float) $row[7] ?? 0;
                $count++;
            }
    
            $roiAvg = $cost > 0 ? round($grossRevenue / $cost, 2) : 0;
    
            // Gọi cập nhật tổng hợp
            \App\Services\LivePerformanceAggregator::updateFromAuto(
                $room_id,
                $date,
                $hour,
                $type,
                $cost,
                $grossRevenue,
                $roiAvg
            );
    
            return redirect()->route('live_performance.daily', $room_id)
                             ->with('success', 'Import dữ liệu Ads Auto thành công!');
        }
    
        return redirect()->route('live_performance.daily', $room_id)
                         ->with('error', 'Không thể đọc file Excel.');
    }
    
    public function destroy(Request $request, $id)
    {
        $data = AdsAutoDay::findOrFail($id);
        $data->delete();

        $room_id = $request->input('room_id');
        return redirect()->route('ads_auto_data_days.index', $room_id)
                         ->with('success', 'Bản ghi đã được xóa thành công.');
    }

    public function trashed($room_id)
    {
        $trashedData = AdsAutoDay::onlyTrashed()
            ->where('room_id', $room_id)
            ->paginate(10);

        return view('ads_auto_data_days.trashed', compact('trashedData', 'room_id'));
    }

    public function restore($id)
    {
        $data = AdsAutoDay::withTrashed()->findOrFail($id);
        $data->restore();

        return redirect()->route('ads_auto_data_days.trashed', $data->room_id)
                         ->with('success', 'Khôi phục bản ghi thành công.');
    }

    public function forceDelete($id)
    {
        $data = AdsAutoDay::withTrashed()->findOrFail($id);
        $room_id = $data->room_id;
        $data->forceDelete();

        return redirect()->route('ads_auto_data_days.trashed', $room_id)
                         ->with('success', 'Bản ghi đã được xóa vĩnh viễn.');
    }
}
