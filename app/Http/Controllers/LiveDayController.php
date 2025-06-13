<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LiveDay;
use Carbon\Carbon;

class LiveDayController extends Controller
{
    public function index() {
        $liveDays = LiveDay::orderBy('live_date', 'asc')->get();
        return view('live_days.index', compact('liveDays'));
    }

    public function create() {
        return view('live_days.create');
    }

    public function store(Request $request) {
        $request->validate([
            'live_date' => 'required|date|unique:live_days,live_date',
            'gmv_target' => 'nullable|numeric',
            'day_type' => 'required|in:normal,sale,key',
        ]);

        LiveDay::create($request->all());
        return redirect()->route('live_days.index')->with('success', 'Ngày Live đã được thêm.');
    }

    public function edit($live_date) {
        $liveDay = LiveDay::where('live_date', $live_date)->firstOrFail();
        return view('live_days.edit', compact('liveDay'));
    }

    public function update(Request $request, $live_date) {
        $request->validate([
            'gmv_target' => 'nullable|numeric',
            'day_type' => 'required|in:normal,sale,key',
        ]);

        LiveDay::where('live_date', $live_date)->update($request->only(['gmv_target', 'day_type']));
        return redirect()->route('live_days.index')->with('success', 'Ngày Live đã được cập nhật.');
    }

    public function destroy($live_date) {
        LiveDay::where('live_date', $live_date)->delete();
        return redirect()->route('live_days.index')->with('success', 'Ngày Live đã bị xóa.');
    }

    public function generateDays(Request $request) {
        $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);

        $year = Carbon::parse($request->month)->year;
        $month = Carbon::parse($request->month)->month;
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($year, $month, $day)->format('Y-m-d');

            LiveDay::firstOrCreate([
                'live_date' => $date,
            ], [
                'gmv_target' => null,
                'day_type' => 'normal',
            ]);
        }

        return redirect()->route('live_days.index')->with('success', 'Đã tạo ngày live cho tháng ' . $request->month);
    }
}
