<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class CheckRoomAccess
{
//     public function handle(Request $request, Closure $next)
//     {
//         // ✅ Nếu là admin thì qua
//         if (Auth::guard('admin')->check()) {
//             return $next($request);
//         }

//         // ✅ Nếu không đăng nhập
//         if (!Auth::check()) {
//             return redirect()->route('login');
//         }

//         $user = Auth::guard('web')->user();

//         // ✅ Lấy room_id từ route
//         // $roomId = $request->route('room') ?? $request->route('room_id') ?? $request->route('id');
// $rawRoom = $request->route('room') ?? $request->route('room_id') ?? $request->route('id');
// $roomId = is_object($rawRoom) ? $rawRoom->id : $rawRoom;

//         if ($roomId) {
//             /** @var \App\Models\Staff $user */
//             $hasAccess = $user->rooms()->where('rooms.id', $roomId)->exists();

//             if (!$hasAccess) {
//                 abort(403, 'Bạn không có quyền truy cập phòng này.');
//             }

//             return $next($request);
//         }

//         // ✅ Cho phép một số route không có room_id
//         $allowedRoutes = [
//             'rooms.index',
//             'rooms.dashboard',
//             'rooms.show',
//             'ads_manual_data_days.index',
//             'ads_manual_data_days.import',
//             'ads_manual_data_days.import_index',
//             'live_performance.daily',
//             'live_performance.hourly',
//             'staff_roles.index', // 👈 bổ sung
//         ];

//         if (!in_array(Route::currentRouteName(), $allowedRoutes)) {
//             abort(403, 'Bạn không có quyền truy cập chức năng này.');
//         }

//         return $next($request);
//     }

public function handle(Request $request, Closure $next)
{
    // ✅ Nếu là admin thì bỏ qua kiểm tra phòng
    if (Auth::guard('admin')->check()) {
        return $next($request);
    }

    // ✅ Nếu không đăng nhập staff
    if (!Auth::guard('web')->check()) {
        return redirect()->route('login');
    }

    $user = Auth::guard('web')->user();

    // ✅ Lấy room_id từ route
    $rawRoom = $request->route('room') ?? $request->route('room_id') ?? $request->route('id');
    $roomId = is_object($rawRoom) ? $rawRoom->id : $rawRoom;

    if ($roomId) {
        /** @var \App\Models\Staff $user */
        $hasAccess = $user->rooms()->where('rooms.id', $roomId)->exists();

        if (!$hasAccess) {
            abort(403, 'Bạn không có quyền truy cập phòng này.');
        }

        return $next($request);
    }

    // ✅ Cho phép một số route không có room_id
    $allowedRoutes = [
        'rooms.index',
        'rooms.dashboard',
        'rooms.show',
        'ads_manual_data_days.index',
        'ads_manual_data_days.import',
        'ads_manual_data_days.import_index',
        'live_performance.daily',
        'live_performance.hourly',
        'staff_roles.index',
    ];

    if (!in_array(Route::currentRouteName(), $allowedRoutes)) {
        abort(403, 'Bạn không có quyền truy cập chức năng này.');
    }

    return $next($request);
}

}
