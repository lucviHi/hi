<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PlatformController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StaffRoleController;
use App\Http\Controllers\LiveDayController;
use App\Http\Controllers\AdsManualDataDayController;
use App\Http\Controllers\LayoutController;

// Route::get('/', function () {
//     //return view('welcome');
//     return view('home');
// });


Route::get('/', [LayoutController::class, 'index'])->name('home');


Route::get('/admin', function () {
    return view('rooms.index');
});



Route::resource('rooms', RoomController::class);
Route::resource('projects', ProjectController::class);


Route::resource('platforms', PlatformController::class);

Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/platforms', [PlatformController::class, 'index'])->name('platforms.index');
Route::resource('roles', RoleController::class);
Route::resource('staffs', StaffController::class);
Route::resource('staff_roles', StaffRoleController::class);


Route::get('/rooms/{id}', [RoomController::class, 'show'])->name('rooms.show');
Route::get('/rooms/{id}/show-live', [RoomController::class, 'show_live'])->name('rooms.dashboard');
// Route::get('/rooms/{id}/report-daily', [RoomController::class, 'report_daily'])->name('rooms.report-daily');
// Route::get('/rooms/{id}/report-hourly', [RoomController::class, 'report_hourly'])->name('rooms.report-hourly');

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




Route::resource('live_days', LiveDayController::class);



Route::get('/live_days', [LiveDayController::class, 'index'])->name('live_days.index');
Route::get('/live_days/create', [LiveDayController::class, 'create'])->name('live_days.create');
Route::post('/live_days', [LiveDayController::class, 'store'])->name('live_days.store');
Route::get('/live_days/{live_date}/edit', [LiveDayController::class, 'edit'])->name('live_days.edit');
Route::put('/live_days/{live_date}', [LiveDayController::class, 'update'])->name('live_days.update');
Route::delete('/live_days/{live_date}', [LiveDayController::class, 'destroy'])->name('live_days.destroy');
Route::post('/live_days/generate', [LiveDayController::class, 'generateDays'])->name('live_days.generate');



// Route::get('/ads_manual_data_days/import', [AdsManualDataDayController::class, 'importView'])->name('ads_manual_data_days.importView');
// Route::post('/ads_manual_data_days/import', [AdsManualDataDayController::class, 'import'])->name('ads_manual_data_days.import');
// // Route::get('/ads_manual_data_days', [AdsManualDataDayController::class, 'index'])->name('ads_manual_data_days.index');

//Route::resource('/import-ads-manual', AdsManualDataDayController::class);
// Route::get('/ads_manual_data_days', [AdsManualDataDayController::class, 'index'])->name('ads_manual_data_days.index');
// Route::post('/ads_manual_data_days/import', [AdsManualDataDayController::class, 'import'])->name('ads_manual_data_days.import');


use App\Http\Controllers\AdsManualDataController;
Route::resource('ads_manual_data_days', AdsManualDataDayController::class);

Route::get('/import-ads-manual/{room_id}', action: [AdsManualDataDayController::class, 'index'])->name('ads_manual_data_days.index');
Route::get('/import-ads-manual/import_index/{room_id}', [AdsManualDataDayController::class, 'import_index'])->name('ads_manual_data_days.import_index');
Route::post('/import-ads-manual/import/{room_id}', [AdsManualDataDayController::class, 'import'])->name('ads_manual_data_days.import');



Route::get('ads_manual_data_days/{room_id}/trashed', [AdsManualDataDayController::class, 'trashed'])->name('ads_manual_data_days.trashed');
Route::post('ads_manual_data_days/{room_id}', [AdsManualDataDayController::class, 'destroy'])->name('ads_manual_data_days.destroy');
Route::post('ads_manual_data_days/{room_id}/restore', [AdsManualDataDayController::class, 'restore'])->name('ads_manual_data_days.restore');
Route::delete('ads_manual_data_days/{room_id}/force-delete', [AdsManualDataDayController::class, 'forceDelete'])->name('ads_manual_data_days.forceDelete');

use App\Http\Controllers\AdsGmvMaxDataDayController;

    Route::resource('ads_gmv_max_data_days', AdsGmvMaxDataDayController::class);

    Route::get('/ads-gmv-max-data-days/{room_id}', action: [AdsGmvMaxDataDayController::class, 'index'])->name('ads_gmv_max_data_days.index');
    Route::get('/ads-gmv-max-data-days/import_index/{room_id}', [AdsGmvMaxDataDayController::class, 'import_index'])->name('ads_gmv_max_data_days.import_index');
    Route::post('/ads-gmv-max-data-days/import/{room_id}', [AdsGmvMaxDataDayController::class, 'import'])->name('ads_gmv_max_data_days.import');
    
    
    
    Route::get('ads_gmv_max_data_days/{room_id}/trashed', [AdsGmvMaxDataDayController::class, 'trashed'])->name('ads_gmv_max_data_days.trashed');
    Route::post('ads_gmv_max_data_days/{room_id}', [AdsGmvMaxDataDayController::class, 'destroy'])->name('ads_gmv_max_data_dayss.destroy');
    Route::post('ads_gmv_max_data_days/{room_id}/restore', [AdsGmvMaxDataDayController::class, 'restore'])->name('ads_gmv_max_data_days.restore');
    Route::delete('ads_gmv_max_data_days/{room_id}/force-delete', [AdsGmvMaxDataDayController::class, 'forceDelete'])->name('ads_gmv_max_data_days.forceDelete');

    
use App\Http\Controllers\StreamerDataDayController;

Route::resource('streamer_data_days', StreamerDataDayController::class);

Route::get('/streamer-data-days/{room_id}', [StreamerDataDayController::class, 'index'])->name('streamer_data_days.index');
Route::get('/streamer-data-days/import_index/{room_id}', [StreamerDataDayController::class, 'import_index'])->name('streamer_data_days.import_index');
Route::post('/streamer-data-days/import/{room_id}', [StreamerDataDayController::class, 'import'])->name('streamer_data_days.import');

Route::get('streamer_data_days/{room_id}/trashed', [StreamerDataDayController::class, 'trashed'])->name('streamer_data_days.trashed');
Route::post('streamer_data_days/{room_id}', [StreamerDataDayController::class, 'destroy'])->name('streamer_data_days.destroy');
Route::post('streamer_data_days/{room_id}/restore', [StreamerDataDayController::class, 'restore'])->name('streamer_data_days.restore');
Route::delete('streamer_data_days/{room_id}/force-delete', [StreamerDataDayController::class, 'forceDelete'])->name('streamer_data_days.forceDelete');

use App\Http\Controllers\AdsDataDayController;


Route::prefix('ads-data-days')->group(function () {
    Route::get('/{room_id}', [AdsDataDayController::class, 'index'])->name('ads-data-days.index'); // Danh sách dữ liệu quảng cáo theo room_id
    Route::get('/{room_id}/create', [AdsDataDayController::class, 'create'])->name('ads-data-days.create'); // Form thêm dữ liệu
    Route::post('/{room_id}/store', [AdsDataDayController::class, 'store'])->name('ads-data-days.store'); // Lưu dữ liệu mới
    Route::get('/{room_id}/{id}/edit', [AdsDataDayController::class, 'edit'])->name('ads-data-days.edit'); // Form chỉnh sửa
    Route::put('/{room_id}/{id}', [AdsDataDayController::class, 'update'])->name('ads-data-days.update'); // Cập nhật dữ liệu
    Route::delete('/{room_id}/{id}', [AdsDataDayController::class, 'destroy'])->name('ads-data-days.destroy'); // Xóa dữ liệu

    // Import dữ liệu từ file CSV/Excel
    Route::post('/{room_id}/import', [AdsDataDayController::class, 'import'])->name('ads-data-days.import'); 
});
use App\Http\Controllers\AdsAutoDataDayController;

// Nhóm route cho ads auto
Route::prefix('ads-auto-data-days')->group(function () {
    Route::get('/{room_id}', [AdsAutoDataDayController::class, 'index'])->name('ads_auto_data_days.index');
    Route::get('/import/{room_id}', [AdsAutoDataDayController::class, 'import_index'])->name('ads_auto_data_days.import_index');
    Route::post('/import/{room_id}', [AdsAutoDataDayController::class, 'import'])->name('ads_auto_data_days.import');

    Route::delete('/{id}', [AdsAutoDataDayController::class, 'destroy'])->name('ads_auto_data_days.destroy');

    Route::get('/trashed/{room_id}', [AdsAutoDataDayController::class, 'trashed'])->name('ads_auto_data_days.trashed');
    Route::put('/restore/{id}', [AdsAutoDataDayController::class, 'restore'])->name('ads_auto_data_days.restore');
    Route::delete('/force-delete/{id}', [AdsAutoDataDayController::class, 'forceDelete'])->name('ads_auto_data_days.forceDelete');
});

use App\Http\Controllers\LivePerformanceDayController;
Route::prefix('live-performance')->group(function () {
    Route::get('daily/{room_id}', [LivePerformanceDayController::class, 'daily'])->name('live_performance.daily');
    Route::get('hourly/{room_id}', [LivePerformanceDayController::class, 'hourly'])->name('live_performance.hourly');
});

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminAuthController;

// Hiển thị form đăng nhập
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

// Xử lý đăng nhập
Route::post('/login', [AuthController::class, 'login']);

// Đăng xuất
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard'); // ví dụ
        })->name('admin.dashboard');
    });
});

