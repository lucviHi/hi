<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\LivePerformanceSnap;

class DashboardController extends Controller
{
    public function showRoomDashboard($room_id)
    {
        // Truy xuất thông tin phòng dựa trên room_id
        $room = Room::findOrFail($room_id);

        // Truy xuất các dữ liệu liên quan đến phòng
        $staffCount = $room->staff()->count();
        $todayRevenue = $room->calculateTodayRevenue();
        $todayLiveHours = $room->calculateTodayLiveHours();
        $todayAdCost = $room->calculateTodayAdCost();
        $staffPerformanceData = $room->getStaffPerformanceData();
        $livePerformanceData = $room->getLivePerformanceData();
        $adPerformanceData = $room->getAdPerformanceData();
        $channelExpenses = $room->channelExpenses()->get();

        // Chuyển dữ liệu đến view
        return view('room_dashboard', compact(
            'room',
            'staffCount',
            'todayRevenue',
            'todayLiveHours',
            'todayAdCost',
            'staffPerformanceData',
            'livePerformanceData',
            'adPerformanceData',
            'channelExpenses'
        ));
    }

 }

