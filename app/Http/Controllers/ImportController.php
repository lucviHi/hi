<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdsManualDataDay;
use Carbon\Carbon;
use Shuchkin\SimpleXLSX;

class ImportController extends Controller
{
    public function importAdsManualData(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx'
        ]);

        $file = $request->file('file');

        if ($xlsx = SimpleXLSX::parse($file)) {
            $rows = $xlsx->rows();
            foreach ($rows as $index => $row) {
                if ($index === 0) continue; // Bỏ qua header

                AdsManualDataDay::create([
                    'date' => Carbon::createFromFormat('Y-m-d', $row[0])->format('Y-m-d'),
                    'cost_usd' => $row[1] ?? 0,
                    'cost_local' => $row[2] ?? 0,
                    'cpc_usd' => $row[3] ?? 0,
                    'cpa_usd' => $row[4] ?? 0,
                    'total_purchases' => $row[5] ?? 0,
                    'cost_per_payment' => $row[6] ?? 0,
                    'impressions' => $row[7] ?? 0,
                    'ctr' => $row[8] ?? 0,
                    'cpm' => $row[9] ?? 0,
                    'cpc' => $row[10] ?? 0,
                    'clicks' => $row[11] ?? 0,
                    'conversions' => $row[12] ?? 0,
                    'cvr' => $row[13] ?? 0,
                    'cpa' => $row[14] ?? 0,
                    'roas_purchase' => $row[15] ?? 0,
                    'roas_payment' => $row[16] ?? 0,
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
                ]);
            }
        }

        return redirect()->route('ads_manual_data_days.index')->with('success', 'Import thành công!');
    }
    public function daily_report($room_id) {
        return view('layouts.app', compact('rooms'));
    }
}
