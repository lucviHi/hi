<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Staff;
use App\Models\StaffRole;
 
class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
{
    if (Auth::guard('admin')->check()) {
        return back()->withErrors(['email' => 'Vui lòng đăng xuất khỏi tài khoản admin trước khi đăng nhập nhân viên.']);
    }

    $credentials = $request->only('email', 'password');

    if (Auth::guard('web')->attempt($credentials)) {
         /** @var \App\Models\Staff $user */
        $user = Auth::guard('web')->user();
        $room = $user->rooms()->first();

        if (!$room) {
            Auth::guard('web')->logout();
            return back()->withErrors(['email' => 'Bạn chưa được phân vào kênh nào.']);
        }

        return redirect()->route('rooms.show', ['room_id' => $room->id]);
    }

    return back()->withErrors(['email' => 'Email hoặc mật khẩu không đúng.']);
}

    

public function logout()
{
    Auth::guard('web')->logout();
    return redirect()->route('login');
}

    
}