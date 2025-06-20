<?php

namespace App\Http\Controllers;

use App\Models\AffiliateOrder;
use App\Models\Project;
use Illuminate\Http\Request;
use Shuchkin\SimpleXLSX;
use Carbon\Carbon;

class AffiliateOrderController extends Controller
{
    public function index(Request $request, $project_id)
    {
        $project = Project::findOrFail($project_id);
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

        $orders = AffiliateOrder::where('project_id', $project_id)
            ->whereBetween('order_created_at', [$startDate, $endDate])
            ->orderByDesc('order_created_at')
            ->get();

        return view('orders.aff_orders', compact('orders', 'project', 'startDate', 'endDate','project_id'));
    }

    public function import(Request $request, $project_id)
    {set_time_limit(300); // Cho phép chạy tối đa 5 phút

        $request->validate([
            'file' => 'required|mimes:xlsx',
        ]);

        $file = $request->file('file');

        if ($xlsx = SimpleXLSX::parse($file->getPathname())) {
            $rows = $xlsx->rows();
            array_shift($rows); // Bỏ header

            foreach ($rows as $row) {
                if (empty($row[0])) continue;
    //             $date = null;
    // try {
    //    $date = Carbon::parse(str_replace('/', '-', trim($row[30])));

    // } catch (\Exception $e) {
    //     continue; // Bỏ qua dòng nếu ngày không đúng định dạng
    // }  
   $dateString = trim($row[30]); // Giả sử cột này là ngày giờ "19/06/2025 13:46:49"

try {
    $timestamp = \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', $dateString)->format('Y-m-d');;
} catch (\Exception $e) {
    $timestamp = null; // fallback nếu không đúng format
}

                AffiliateOrder::updateOrCreate([
                    'project_id' => $project_id,
                    'order_id' => $row[0],
                ], [
                    'product_id'       => $row[1] ?? null,
                    'product_name'     => $row[2] ?? null,
                    'sku'              => $row[3] ?? null,
                    'sku_id'           => $row[4] ?? null,
                    'seller_sku'       => $row[12] ?? null,
                    'price'            => $row[6] ?? 0,
                    'payment_amount'   => $row[7] ?? 0,
                    'quantity'         => $row[9] ?? 1,
                    'order_status'     => $row[11] ?? null,
                    'content_type'     => $row[13] ?? null,
                    'commission_rate'  => $row[18] ?? null,
                    'order_created_at' => $timestamp  ?? null,
                ]);
            }

            return back()->with('success', 'Import đơn hàng affiliate thành công!');
        }

        return back()->with('error', 'Không thể đọc file Excel.');
    }

    public function destroy($id)
    {
        $order = AffiliateOrder::findOrFail($id);
        $project_id = $order->project_id;
        $order->delete();

        return redirect()->route('orders.aff_orders', $project_id)
            ->with('success', 'Đã xóa đơn hàng.');
    }
}
