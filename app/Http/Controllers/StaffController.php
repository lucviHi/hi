<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    // Hiển thị danh sách nhân viên
    public function index(Request $request)
    {
        $query = Staff::query();

        // Lọc theo từ khóa (tìm theo tên hoặc mã nhân viên)
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'LIKE', "%$search%")
                  ->orWhere('staff_code', 'LIKE', "%$search%");
        }

        $staffs = $query->paginate(10); // Phân trang 10 nhân viên mỗi trang

        return view('staffs.index', compact('staffs'));
    }

    public function search(Request $request) {
        $staffs = Staff::where('name', 'like', "%{$request->term}%")->get();
        return response()->json($staffs);
    }

    // Hiển thị form thêm nhân viên
    public function create()
    {
        return view('staffs.create');
    }

    // Lưu nhân viên mới
    public function store(Request $request)
    {
        $request->validate([
            'staff_code' => 'required|unique:staffs,staff_code',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:staffs,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        Staff::create([
            'staff_code' => $request->staff_code,
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return redirect()->route('staffs.index')->with('success', 'Nhân viên đã được thêm!');
    }

    // Hiển thị form chỉnh sửa nhân viên
    public function edit(Staff $staff)
    {
        return view('staffs.edit', compact('staff'));
    }

    // Cập nhật nhân viên
    public function update(Request $request, Staff $staff)
    {
        $request->validate([
            'staff_code' => 'required|unique:staffs,staff_code,' . $staff->id,
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:staffs,email,' . $staff->id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $data = $request->only(['staff_code', 'name', 'email']);

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $staff->update($data);

        return redirect()->route('staffs.index')->with('success', 'Nhân viên đã được cập nhật!');
    }

    // Xóa nhân viên
    public function destroy(Staff $staff)
    {
        $staff->delete();
        return redirect()->route('staffs.index')->with('success', 'Nhân viên đã được xóa!');
    }
}
