<?php

namespace App\Http\Controllers;

use App\Models\Platform;
use Illuminate\Http\Request;

class PlatformController extends Controller
{
    // Hiển thị danh sách nền tảng
    public function index()
    {
        $platforms = Platform::all();
        return view('platforms.index', compact('platforms'));
    }

    // Hiển thị form tạo mới
    public function create()
    {
        return view('platforms.create');
    }

    // Lưu nền tảng mới vào database
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:platforms|max:255',
        ]);

        Platform::create($request->all());

        return redirect()->route('platforms.index')
                         ->with('success', 'Nền tảng đã được thêm.');
    }

    // Hiển thị chi tiết một nền tảng
    public function show(Platform $platform)
    {
        return view('platforms.show', compact('platform'));
    }

    // Hiển thị form chỉnh sửa nền tảng
    public function edit(Platform $platform)
    {
        return view('platforms.edit', compact('platform'));
    }

    // Cập nhật nền tảng
    public function update(Request $request, Platform $platform)
    {
        $request->validate([
            'name' => 'required|max:255|unique:platforms,name,' . $platform->id,
        ]);

        $platform->update($request->all());

        return redirect()->route('platforms.index')
                         ->with('success', 'Cập nhật thành công.');
    }

    // Xóa nền tảng
    public function destroy(Platform $platform)
    {
        $platform->delete();
        return redirect()->route('platforms.index')
                         ->with('success', 'Nền tảng đã bị xóa.');
    }
}
