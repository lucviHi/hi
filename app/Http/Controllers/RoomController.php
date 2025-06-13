<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Project;



class RoomController extends Controller
{
    // 1️⃣ Danh sách Room
    public function index()
    {
        $rooms = Room::with('project')->paginate(10); // Lấy Room kèm Project
        return view('rooms.index', compact('rooms'));
        
    }
    
    public function create()
    {
        $projects = Project::all();
        return view('rooms.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
        ]);

        Room::create($request->all());

        return redirect()->route('rooms.index')->with('success', 'Phòng đã được tạo.');
    }

    public function edit(Room $room)
    {
        $projects = Project::all();
        return view('rooms.edit', compact('room', 'projects'));
    }

    public function update(Request $request, Room $room)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
        ]);

        $room->update($request->all());

        return redirect()->route('rooms.index')->with('success', 'Phòng đã được cập nhật.');
    }

    public function destroy(Room $room)
    {
        $room->delete();

        return redirect()->route('rooms.index')->with('success', 'Phòng đã được xóa.');
    }
    // public function show(Room $room)
    // {
    //     $room->load('staffRoles.staff', 'staffRoles.role');
    //     return view('rooms.show', compact('room'));
    // // }
    // public function show($room_id)
    // { 
    //     // Tìm phòng theo ID, nếu không tìm thấy sẽ trả về lỗi 404
    //     $room = Room::findOrFail($room_id);
        
    //     // Trả về view 'rooms.show' với dữ liệu phòng
    //     return view('rooms.dashboard', compact('room'));
    // }
    public function show($room_id)
    {
        if (auth()->guard('admin')->check()) {
            /** @var \App\Models\Admin $admin */
            $admin = auth()->guard('admin')->user();
            $room = \App\Models\Room::findOrFail($room_id);
        } else {
            /** @var \App\Models\Staff $user */
            $user = auth()->guard('web')->user();
            $room = $user->rooms()->where('rooms.id', $room_id)->firstOrFail();
        }
    
        return view('rooms.dashboard', compact('room'));
    }
    

    public function show_live($room_id)
    {
        // Tìm phòng theo ID, nếu không tìm thấy sẽ trả về lỗi 404
        $room = Room::findOrFail($room_id);

        // Trả về view 'rooms.show' với dữ liệu phòng
        return view('rooms.show', compact('room'));
    }
    public function report_daily($room_id)
    {
        // Tìm phòng theo ID, nếu không tìm thấy sẽ trả về lỗi 404
        $room = Room::findOrFail($room_id);

        // Trả về view 'rooms.show' với dữ liệu phòng
        return view('room_report.report_daily', compact('room'));
    }
    public function report_hourly($room_id)
    {
        // Tìm phòng theo ID, nếu không tìm thấy sẽ trả về lỗi 404
        $room = Room::findOrFail($room_id);

        // Trả về view 'rooms.show' với dữ liệu phòng
        return view('room_report.report_hourly', compact('room'));
    }

   
    
}
