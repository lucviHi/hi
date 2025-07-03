<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
{
    if (Auth::guard('web')->check()) {
        return back()->withErrors(['email' => 'Vui lòng đăng xuất khỏi tài khoản nhân viên trước khi đăng nhập admin.']);
    }

    $credentials = $request->only('email', 'password');

    if (Auth::guard('admin')->attempt($credentials)) {
        return redirect()->route('admin.dashboard');
    }

    return back()->withErrors(['email' => 'Email hoặc mật khẩu sai.']);
}

public function logout()
{
    Auth::guard('admin')->logout();
    return redirect()->route('admin.login');
}

}
