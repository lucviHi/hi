<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LiveTargetDay;
use App\Models\Room;
use Carbon\Carbon;

class LiveTargetDayController extends Controller
{
    // public function index($room_id)
    // {
    //     $room = Room::findOrFail($room_id);
    //     $targets = LiveTargetDay::where('room_id', $room_id)->orderBy('date')->get();

    //     return view('live_target_days.index', compact('room', 'targets'));
    // }
public function index(Request $request, $room_id)
{
    $room = Room::findOrFail($room_id);

    $month = $request->input('month');
    $targets = LiveTargetDay::where('room_id', $room_id)
        ->when($month, function ($query) use ($month) {
            $query->whereMonth('date', Carbon::parse($month)->month)
                  ->whereYear('date', Carbon::parse($month)->year);
        })
        ->orderBy('date')
        ->get();

    return view('live_target_days.index', compact('room', 'targets', 'month'));
}

    public function create($room_id)
    {
        $room = Room::findOrFail($room_id);
        return view('live_target_days.create', compact('room'));
    }

    // public function store(Request $request, $room_id)
    // {
        
    //     $request->validate([
    //         'date' => 'required|date',
    //         'gmv_target' => 'nullable|numeric',
    //         'cost_limit' => 'nullable|numeric',
    //         'team_count' => 'nullable|integer',
    //         'day_type' => 'required|in:normal,sale,key',
    //         'note' => 'nullable|string',
    //     ]);


    //     LiveTargetDay::updateOrCreate(
    //         ['room_id' => $room_id, 'date' => $request->date],
    //         $request->only('gmv_target', 'cost_limit', 'team_count', 'day_type', 'note') + ['room_id' => $room_id]
    //     );

    //     return redirect()->route('live_target_days.index', $room_id)->with('success', 'Đã lưu mục tiêu ngày.');
    // }
public function store(Request $request, $room_id)
{
    // Làm sạch số chứa dấu phẩy trước khi validate
    $request->merge([
        'gmv_target' => $request->gmv_target ? str_replace(',', '', $request->gmv_target) : null,
        'cost_limit' => $request->cost_limit ? str_replace(',', '', $request->cost_limit) : null,
        'team_count' => $request->team_count ? str_replace(',', '', $request->team_count) : null,
    ]);

    $request->validate([
        'date' => 'required|date',
        'gmv_target' => 'nullable|numeric',
        'cost_limit' => 'nullable|numeric',
        'team_count' => 'nullable|integer',
        'day_type' => 'required|in:normal,sale,key',
        'note' => 'nullable|string',
    ]);

    LiveTargetDay::updateOrCreate(
        ['room_id' => $room_id, 'date' => $request->date],
        $request->only('gmv_target', 'cost_limit', 'team_count', 'day_type', 'note') + ['room_id' => $room_id]
    );

    return redirect()->route('live_target_days.index', $room_id)->with('success', 'Đã lưu mục tiêu ngày.');
}

    public function edit($room_id, $date)
    {
        $room = Room::findOrFail($room_id);
        $target = LiveTargetDay::where('room_id', $room_id)
                               ->where('date', $date)
                               ->firstOrFail();

        return view('live_target_days.edit', compact('room', 'target'));
    }

    public function update(Request $request, $room_id, $date)
    {
        $request->validate([
            'gmv_target' => 'nullable|numeric',
            'cost_limit' => 'nullable|numeric',
            'team_count' => 'nullable|integer',
            'day_type' => 'required|in:normal,sale,key',
            'note' => 'nullable|string',
        ]);

        $target = LiveTargetDay::where('room_id', $room_id)
                               ->where('date', $date)
                               ->firstOrFail();

       // $target->update($request->only('gmv_target', 'cost_limit', 'team_count', 'day_type', 'note'));
$cleaned = $request->only('gmv_target', 'cost_limit', 'team_count', 'day_type', 'note');

// Loại bỏ dấu phẩy trong chuỗi số
$cleaned['gmv_target'] = isset($cleaned['gmv_target']) ? str_replace(',', '', $cleaned['gmv_target']) : null;
$cleaned['cost_limit'] = isset($cleaned['cost_limit']) ? str_replace(',', '', $cleaned['cost_limit']) : null;
$cleaned['team_count'] = isset($cleaned['team_count']) ? str_replace(',', '', $cleaned['team_count']) : null;

$target->update($cleaned);

        return redirect()->route('live_target_days.index', $room_id)->with('success', 'Cập nhật thành công.');
    }

    public function destroy($room_id, $date)
    {
        $target = LiveTargetDay::where('room_id', $room_id)
                               ->where('date', $date)
                               ->firstOrFail();

        $target->delete();

        return redirect()->route('live_target_days.index', $room_id)->with('success', 'Đã xóa mục tiêu.');
    }

public function bulkUpdate(Request $request, $room_id)
{
    foreach ($request->input('entries', []) as $entry) {
    $target = LiveTargetDay::where('room_id', $room_id)
                ->where('date', $entry['date'])
                ->first();

    if ($target) {
        $target->update([
            'gmv_target'   => isset($entry['gmv_target']) ? (int) str_replace(',', '', $entry['gmv_target']) : null,
            'cost_limit'   => isset($entry['cost_limit']) ? (int) str_replace(',', '', $entry['cost_limit']) : null,
            'team_count'   => is_numeric($entry['team_count']) ? (int) $entry['team_count'] : null,
            'day_type'     => $entry['day_type'] ?? 'normal',
            'note'         => $entry['note'] ?? null,
        ]);
    }
}

    return redirect()->route('live_target_days.index', $room_id)->with('success', 'Cập nhật mục tiêu ngày thành công!');
}

    public function generate(Request $request, $room_id)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);

        $year = Carbon::parse($request->month)->year;
        $month = Carbon::parse($request->month)->month;
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($year, $month, $day)->format('Y-m-d');

            LiveTargetDay::firstOrCreate([
                'room_id' => $room_id,
                'date' => $date,
            ], [
                'day_type' => 'normal',
            ]);
        }

        return redirect()->route('live_target_days.index', $room_id)->with('success', 'Tạo mục tiêu ngày cho tháng ' . $request->month);
    }
}
