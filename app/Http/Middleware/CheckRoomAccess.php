<?php


namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class CheckRoomAccess
{
    public function handle(Request $request, Closure $next)
    {  
        // ✅ Nếu là admin (dùng guard 'admin')
        if (Auth::guard('admin')->check()) {
            return $next($request); // Cho admin qua tất
        }

        // ✅ Nếu là staff (guard 'web')
        if (!Auth::check()) {
         
            return redirect()->route('login');
        }
        $user = Auth::guard('web')->user();
        $roomId = $request->route('room_id') ?? $request->route('id');
      
        if ($roomId) {
             /** @var \App\Models\Staff $user */
            $hasAccess = $user->staffRoles()->where('room_id', $roomId)->exists();
          
            if (!$hasAccess) {
                abort(403, 'Bạn không có quyền truy cập phòng này.');
            }
         
            return $next($request);
        }

        // ✅ Chỉ cho phép một số route name
        $allowedRoutes = [
            'rooms.index',
            'rooms.dashboard',
            'rooms.show',
            'ads_manual_data_days.index',
            'ads_manual_data_days.import',
            'ads_manual_data_days.import_index',
            'live_performance.daily',
            'live_performance.hourly',
        ];

        if (!in_array(Route::currentRouteName(), $allowedRoutes)) {
            abort(403, 'Bạn không có quyền truy cập chức năng này.');
        }

        return $next($request);
    }
}
