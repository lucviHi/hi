<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo($request): ?string
    {
        if (! $request->expectsJson()) {
            return $request->is('admin/*') ? route('admin.login') : route('login');
        }

        return null;
    }
}



// <?php

// namespace App\Http\Middleware; 

// use Closure;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use App\Models\StaffRole;
// use App\Models\Staff;

// class CheckRoomAccess
// {
//     public function handle(Request $request, Closure $next)
//     { 
//          // Nếu là admin thì cho qua luôn
//         if (auth('admin')->check()) {
//             return $next($request);
//         }

//         /** @var \App\Models\Staff $user */
//         $user = Auth::user();
//         $roomId = $request->route('id'); // Lấy room_id từ URL
//         // Kiểm tra xem nhân viên có được gán vào room này không
//         $hasAccess = $user->staffRoles()
//             ->where('room_id', $roomId)
//             ->exists();

//         if (!$hasAccess) {
//             abort(403, 'Bạn không có quyền truy cập phòng này.');
//         }

//         return $next($request);
//     }
// }
