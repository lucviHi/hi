<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    RoomController, ProjectController, PlatformController, RoleController, StaffController,
    StaffRoleController, LiveDayController, AdsManualDataDayController, AdsGmvMaxDataDayController,
    StreamerDataDayController, AdsAutoDataDayController, LivePerformanceDayController,
    AuthController, AdminAuthController, LayoutController
};

// ===================== PUBLIC =====================
Route::get('/', [LayoutController::class, 'index'])->name('home');

// ===================== LOGIN =====================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
});
// Danh sách phòng
Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');

// Form tạo phòng
Route::get('/rooms/create', [RoomController::class, 'create'])->name('rooms.create');

// Lưu phòng mới
Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');

// Form chỉnh sửa
Route::get('/rooms/{room}/edit', [RoomController::class, 'edit'])->name('rooms.edit');

// Cập nhật phòng
Route::put('/rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');

// Xoá phòng
Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');

// Hiển thị dashboard phòng (hoặc chi tiết)
Route::get('/rooms/{room_id}', [RoomController::class, 'show'])->name('rooms.show');

// Hiển thị view show-live
Route::get('/rooms/{room_id}/show-live', [RoomController::class, 'show_live'])->name('rooms.dashboard');

Route::prefix('live_performance')->group(function () {
    Route::get('daily/{room_id}', [LivePerformanceDayController::class, 'daily'])->name('live_performance.daily');
    Route::get('hourly/{room_id}', [LivePerformanceDayController::class, 'hourly'])->name('live_performance.hourly');
});
// ===================== ADMIN ROUTES =====================
Route::middleware('auth:admin')->group(function () {
    Route::get('/dashboard', fn() => view('admin.dashboard'))->name('admin.dashboard');

  
    Route::resources([
       // 'rooms' => RoomController::class,
        'projects' => ProjectController::class,
        'platforms' => PlatformController::class,
        'roles' => RoleController::class,
        'staffs' => StaffController::class,
        'staff_roles' => StaffRoleController::class,
        'live_days' => LiveDayController::class,
        'live_performance' => LivePerformanceDayController::class
    ]);

    
    //Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
    Route::post('/live_days/generate', [LiveDayController::class, 'generateDays'])->name('live_days.generate');
    Route::get('/staff/search', [StaffController::class, 'search'])->name('staff.search');

    // Các route liên quan đến room_id

// Hiển thị danh sách nhân viên trong kênh
Route::get('/rooms/{room}/staff_roles', [StaffRoleController::class, 'index'])->name('staff_roles.index');

// Hiển thị form thêm nhân viên vào kênh
Route::get('/rooms/{room}/staff_roles/create', [StaffRoleController::class, 'create'])->name('staff_roles.create');

// Xử lý lưu nhân viên vào kênh
Route::post('/rooms/{room}/staff_roles', [StaffRoleController::class, 'store'])->name('staff_roles.store');

// Xóa nhân viên khỏi kênh
Route::delete('/rooms/{room}/staff_roles/{staffRole}', [StaffRoleController::class, 'destroy'])
    ->name('staff_roles.destroy');
// Hiển thị form chỉnh sửa nhân viên trong kênh
Route::get('/rooms/{room}/staff_roles/{staffRole}/edit', [StaffRoleController::class, 'edit'])->name('staff_roles.edit');

// Xử lý cập nhật thông tin nhân viên trong kênh
Route::put('/rooms/{room}/staff_roles/{staffRole}', [StaffRoleController::class, 'update'])->name('staff_roles.update');

Route::get('/staff/search', [StaffController::class, 'search'])->name('staff.search');



    // Import và báo cáo
  
    Route::resource('ads_manual_data_days', AdsManualDataDayController::class);

    Route::get('/import-ads-manual/{room_id}', action: [AdsManualDataDayController::class, 'index'])->name('ads_manual_data_days.index');
    Route::get('/import-ads-manual/import_index/{room_id}', [AdsManualDataDayController::class, 'import_index'])->name('ads_manual_data_days.import_index');
    Route::post('/import-ads-manual/import/{room_id}', [AdsManualDataDayController::class, 'import'])->name('ads_manual_data_days.import');
    Route::get('ads_manual_data_days/{room_id}/trashed', [AdsManualDataDayController::class, 'trashed'])->name('ads_manual_data_days.trashed');
    Route::post('ads_manual_data_days/{room_id}', [AdsManualDataDayController::class, 'destroy'])->name('ads_manual_data_days.destroy');
    Route::post('ads_manual_data_days/{room_id}/restore', [AdsManualDataDayController::class, 'restore'])->name('ads_manual_data_days.restore');
    Route::delete('ads_manual_data_days/{room_id}/force-delete', [AdsManualDataDayController::class, 'forceDelete'])->name('ads_manual_data_days.forceDelete');

    Route::prefix('ads-auto-data-days')->group(function () {
        Route::get('/{room_id}', [AdsAutoDataDayController::class, 'index'])->name('ads_auto_data_days.index');
        Route::get('/import/{room_id}', [AdsAutoDataDayController::class, 'import_index'])->name('ads_auto_data_days.import_index');
        Route::post('/import/{room_id}', [AdsAutoDataDayController::class, 'import'])->name('ads_auto_data_days.import');
    
        Route::delete('/{id}', [AdsAutoDataDayController::class, 'destroy'])->name('ads_auto_data_days.destroy');
    
        Route::get('/trashed/{room_id}', [AdsAutoDataDayController::class, 'trashed'])->name('ads_auto_data_days.trashed');
        Route::put('/restore/{id}', [AdsAutoDataDayController::class, 'restore'])->name('ads_auto_data_days.restore');
        Route::delete('/force-delete/{id}', [AdsAutoDataDayController::class, 'forceDelete'])->name('ads_auto_data_days.forceDelete');
    });
    

    Route::resource('ads_gmv_max_data_days', AdsGmvMaxDataDayController::class);
    Route::get('/ads-gmv-max-data-days/{room_id}', action: [AdsGmvMaxDataDayController::class, 'index'])->name('ads_gmv_max_data_days.index');
    Route::get('/ads-gmv-max-data-days/import_index/{room_id}', [AdsGmvMaxDataDayController::class, 'import_index'])->name('ads_gmv_max_data_days.import_index');
    Route::post('/ads-gmv-max-data-days/import/{room_id}', [AdsGmvMaxDataDayController::class, 'import'])->name('ads_gmv_max_data_days.import');
    Route::get('ads_gmv_max_data_days/{room_id}/trashed', [AdsGmvMaxDataDayController::class, 'trashed'])->name('ads_gmv_max_data_days.trashed');
    Route::post('ads_gmv_max_data_days/{room_id}', [AdsGmvMaxDataDayController::class, 'destroy'])->name('ads_gmv_max_data_dayss.destroy');
    Route::post('ads_gmv_max_data_days/{room_id}/restore', [AdsGmvMaxDataDayController::class, 'restore'])->name('ads_gmv_max_data_days.restore');
    Route::delete('ads_gmv_max_data_days/{room_id}/force-delete', [AdsGmvMaxDataDayController::class, 'forceDelete'])->name('ads_gmv_max_data_days.forceDelete');

    Route::prefix('ads-auto-data-days')->group(function () {
        Route::get('/{room_id}', [AdsAutoDataDayController::class, 'index'])->name('ads_auto_data_days.index');
        Route::get('/import/{room_id}', [AdsAutoDataDayController::class, 'import_index'])->name('ads_auto_data_days.import_index');
        Route::post('/import/{room_id}', [AdsAutoDataDayController::class, 'import'])->name('ads_auto_data_days.import');
    
        Route::delete('/{id}', [AdsAutoDataDayController::class, 'destroy'])->name('ads_auto_data_days.destroy');
    
        Route::get('/trashed/{room_id}', [AdsAutoDataDayController::class, 'trashed'])->name('ads_auto_data_days.trashed');
        Route::put('/restore/{id}', [AdsAutoDataDayController::class, 'restore'])->name('ads_auto_data_days.restore');
        Route::delete('/force-delete/{id}', [AdsAutoDataDayController::class, 'forceDelete'])->name('ads_auto_data_days.forceDelete');
    });
    

    Route::resource('streamer_data_days', StreamerDataDayController::class);
    Route::get('/streamer-data-days/{room_id}', [StreamerDataDayController::class, 'index'])->name('streamer_data_days.index');
    Route::get('/streamer-data-days/import_index/{room_id}', [StreamerDataDayController::class, 'import_index'])->name('streamer_data_days.import_index');
    Route::post('/streamer-data-days/import/{room_id}', [StreamerDataDayController::class, 'import'])->name('streamer_data_days.import');
    Route::get('streamer_data_days/{room_id}/trashed', [StreamerDataDayController::class, 'trashed'])->name('streamer_data_days.trashed');
    Route::post('streamer_data_days/{room_id}', [StreamerDataDayController::class, 'destroy'])->name('streamer_data_days.destroy');
    Route::post('streamer_data_days/{room_id}/restore', [StreamerDataDayController::class, 'restore'])->name('streamer_data_days.restore');
    Route::delete('streamer_data_days/{room_id}/force-delete', [StreamerDataDayController::class, 'forceDelete'])->name('streamer_data_days.forceDelete');

   
});


// ===================== STAFF ROUTES =====================
Route::middleware(['auth:web'])->group(function () {
    Route::middleware('check.room')->group(function () {
        Route::resource('ads_manual_data_days', AdsManualDataDayController::class);

        Route::get('/import-ads-manual/{room_id}', action: [AdsManualDataDayController::class, 'index'])->name('ads_manual_data_days.index');
        Route::get('/import-ads-manual/import_index/{room_id}', [AdsManualDataDayController::class, 'import_index'])->name('ads_manual_data_days.import_index');
        Route::post('/import-ads-manual/import/{room_id}', [AdsManualDataDayController::class, 'import'])->name('ads_manual_data_days.import');
        Route::get('ads_manual_data_days/{room_id}/trashed', [AdsManualDataDayController::class, 'trashed'])->name('ads_manual_data_days.trashed');
        Route::post('ads_manual_data_days/{room_id}', [AdsManualDataDayController::class, 'destroy'])->name('ads_manual_data_days.destroy');
        Route::post('ads_manual_data_days/{room_id}/restore', [AdsManualDataDayController::class, 'restore'])->name('ads_manual_data_days.restore');
        Route::delete('ads_manual_data_days/{room_id}/force-delete', [AdsManualDataDayController::class, 'forceDelete'])->name('ads_manual_data_days.forceDelete');

        Route::resource('ads_gmv_max_data_days', AdsGmvMaxDataDayController::class);
        Route::get('/ads-gmv-max-data-days/{room_id}', action: [AdsGmvMaxDataDayController::class, 'index'])->name('ads_gmv_max_data_days.index');
        Route::get('/ads-gmv-max-data-days/import_index/{room_id}', [AdsGmvMaxDataDayController::class, 'import_index'])->name('ads_gmv_max_data_days.import_index');
        Route::post('/ads-gmv-max-data-days/import/{room_id}', [AdsGmvMaxDataDayController::class, 'import'])->name('ads_gmv_max_data_days.import');
        Route::get('ads_gmv_max_data_days/{room_id}/trashed', [AdsGmvMaxDataDayController::class, 'trashed'])->name('ads_gmv_max_data_days.trashed');
        Route::post('ads_gmv_max_data_days/{room_id}', [AdsGmvMaxDataDayController::class, 'destroy'])->name('ads_gmv_max_data_dayss.destroy');
        Route::post('ads_gmv_max_data_days/{room_id}/restore', [AdsGmvMaxDataDayController::class, 'restore'])->name('ads_gmv_max_data_days.restore');
        Route::delete('ads_gmv_max_data_days/{room_id}/force-delete', [AdsGmvMaxDataDayController::class, 'forceDelete'])->name('ads_gmv_max_data_days.forceDelete');

        
        Route::prefix('ads-auto-data-days')->group(function () {
            Route::get('/{room_id}', [AdsAutoDataDayController::class, 'index'])->name('ads_auto_data_days.index');
            Route::get('/import/{room_id}', [AdsAutoDataDayController::class, 'import_index'])->name('ads_auto_data_days.import_index');
            Route::post('/import/{room_id}', [AdsAutoDataDayController::class, 'import'])->name('ads_auto_data_days.import');
        
            Route::delete('/{id}', [AdsAutoDataDayController::class, 'destroy'])->name('ads_auto_data_days.destroy');
        
            Route::get('/trashed/{room_id}', [AdsAutoDataDayController::class, 'trashed'])->name('ads_auto_data_days.trashed');
            Route::put('/restore/{id}', [AdsAutoDataDayController::class, 'restore'])->name('ads_auto_data_days.restore');
            Route::delete('/force-delete/{id}', [AdsAutoDataDayController::class, 'forceDelete'])->name('ads_auto_data_days.forceDelete');
        });
        Route::resource('streamer_data_days', StreamerDataDayController::class);
        Route::get('/streamer-data-days/{room_id}', [StreamerDataDayController::class, 'index'])->name('streamer_data_days.index');
        Route::get('/streamer-data-days/import_index/{room_id}', [StreamerDataDayController::class, 'import_index'])->name('streamer_data_days.import_index');
        Route::post('/streamer-data-days/import/{room_id}', [StreamerDataDayController::class, 'import'])->name('streamer_data_days.import');
        Route::get('streamer_data_days/{room_id}/trashed', [StreamerDataDayController::class, 'trashed'])->name('streamer_data_days.trashed');
        Route::post('streamer_data_days/{room_id}', [StreamerDataDayController::class, 'destroy'])->name('streamer_data_days.destroy');
        Route::post('streamer_data_days/{room_id}/restore', [StreamerDataDayController::class, 'restore'])->name('streamer_data_days.restore');
        Route::delete('streamer_data_days/{room_id}/force-delete', [StreamerDataDayController::class, 'forceDelete'])->name('streamer_data_days.forceDelete');

       
    });
});

    