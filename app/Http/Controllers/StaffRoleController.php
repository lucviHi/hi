<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StaffRole;
use App\Models\Room;
use App\Models\Staff;
use App\Models\Role;

class StaffRoleController extends Controller
{
    /**
     * Hiển thị danh sách nhân viên trong kênh
     */
    
    public function index(Room $room)
    {
        $staffRoles = StaffRole::where('room_id', $room->id)->with(['staff', 'role'])->paginate(10);
    
        return view('staff_roles.index', compact('room', 'staffRoles'));
    }
   
    /**
     * Hiển thị form gán nhân viên cho kênh
     */
    public function create(Room $room)
    {
        $staffs = Staff::all();
        $roles = Role::all();
        return view('staff_roles.create', compact('room', 'staffs', 'roles'));
    }

    /**
     * Lưu thông tin nhân viên vào kênh
     */
    public function store(Request $request, Room $room)
    {
        $request->validate([
            'staff_id' => 'required|exists:staffs,id',
            'role_id' => 'required|exists:roles,id',
        ]);
    
        StaffRole::create([
            'room_id' => $room->id, // Lấy room_id từ route
            'staff_id' => $request->staff_id,
            'role_id' => $request->role_id,
        ]);
    
        return redirect()->route('staff_roles.index', $room->id)->with('success', 'Nhân viên đã được gán thành công.');
    }

    /**
     * Hiển thị thông tin chi tiết nhân viên trong kênh
     */
    public function show(Room $room)
{
    // Lấy danh sách nhân viên thuộc kênh này cùng với chức vụ của họ
    $staffRoles = StaffRole::where('room_id', $room->id)->with(['staff', 'role'])->paginate(10); 

    return view('staff_role.index', compact('room', 'staffRoles'));
}


    /**
     * Hiển thị form chỉnh sửa nhân viên trong kênh
     */
    public function edit(Room $room, StaffRole $staffRole)
    {
        $staffs = Staff::all();
        $roles = Role::all();
        return view('staff_roles.edit', compact('room', 'staffRole', 'staffs', 'roles'));
    }

    /**
     * Cập nhật thông tin nhân viên trong kênh
     */
    public function update(Request $request, $room, StaffRole $staffRole)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id'
        ]);
    
        $staffRole->update(['role_id' => $request->role_id]);
    
        return redirect()->route('staff_roles.index', ['room' => $room])
                         ->with('success', 'Cập nhật nhân viên thành công!');
    }
    

    /**
     * Xóa nhân viên khỏi kênh
     */
    public function destroy($room, StaffRole $staffRole)
    {
        $staffRole->delete();
        return redirect()->route('staff_roles.index', ['room' => $room])
                         ->with('success', 'Xóa nhân viên thành công!');
    }
    
}
