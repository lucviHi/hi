<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Room;
class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot()
  {
      // Chia sẻ danh sách rooms với tất cả các view
      View::composer('*', function ($view) {
          $rooms = Room::all(); // Lấy tất cả các phòng từ database
          $view->with('rooms', $rooms);
      });
  }
}
