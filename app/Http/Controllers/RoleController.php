<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        return view('roles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'commission_percentage' => 'required|numeric|min:0|max:100',
        ]);

        Role::create([
            'name' => $request->name,
            'commission_percentage' => $request->commission_percentage,
        ]);

        return redirect()->route('roles.index')->with('success', 'Chức vụ đã được tạo thành công.');
    }

    public function edit(Role $role)
    {
        return view('roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'commission_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $role->update([
            'name' => $request->name,
            'commission_percentage' => $request->commission_percentage,
        ]);

        return redirect()->route('roles.index')->with('success', 'Chức vụ đã được cập nhật.');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Chức vụ đã được xóa.');
    }
}
