@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Điều hướng trang quản lý -->
    <div class="row mb-2">
        <div class="nav nav-pills d-flex align-items-center">
            <a href="{{ route('staff_roles.index', $room->id) }}" class="nav-item nav-link">Quản lý nhân sự</a>
            <span class="mx-2">|</span>
            <a href="#" class="nav-item nav-link">Quản lý ca trực</a>
            <span class="mx-2">|</span>
            <a href="{{ route('live_performance.daily',$room->id) }}" class="nav-item nav-link">Quản lý Livestream</a>
            <span class="mx-2">|</span>
            <a href="{{ route('live_performance.hourly',$room->id) }}" class="nav-item nav-link">Báo cáo khung giờ</a>
        </div>
    </div>
    <!-- Tiêu đề chính -->
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Bảng Điều Khiển Phòng: {{ $room->name }}</h1>
        </div>
    </div>

    <!-- Hàng chứa các thẻ thông tin tổng quan -->
    <div class="row">
        <!-- Tổng số nhân sự -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    {{-- <h3>{{ $staffCount }}</h3> --}}
                    <p>Nhân sự</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
        <!-- Doanh thu hôm nay -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    {{-- <h3>{{ number_format($todayRevenue, 0, ',', '.') }} VND</h3> --}}
                    <p>Doanh thu hôm nay</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>
        <!-- Giờ live hôm nay -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    {{-- <h3>{{ $todayLiveHours }} giờ</h3> --}}
                    <p>Giờ live hôm nay</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        <!-- Chi phí quảng cáo hôm nay -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    {{-- <h3>{{ number_format($todayAdCost, 0, ',', '.') }} VND</h3> --}}
                    <p>Chi phí quảng cáo hôm nay</p>
                </div>
                <div class="icon">
                    <i class="fas fa-ad"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ hiệu suất nhân sự -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Hiệu Suất Nhân Sự</h3>
                </div>
                <div class="card-body">
                    <canvas id="staffPerformanceChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ hiệu suất live -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Hiệu Suất Live (Doanh thu & Giờ live)</h3>
                </div>
                <div class="card-body">
                    <canvas id="livePerformanceChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ hiệu suất quảng cáo -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Hiệu Suất Quảng Cáo</h3>
                </div>
                <div class="card-body">
                    <canvas id="adPerformanceChart"></canvas>
                </div>
            </div>
        </div>
    </div>

 
</div>
@endsection

{{-- @section('scripts') --}}
{{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Dữ liệu cho biểu đồ hiệu suất nhân sự
    var staffPerformanceCtx = document.getElementById('staffPerformanceChart').getContext('2d');
    var staffPerformanceChart = new Chart(staffPerformanceCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($staffPerformanceLabels) !!},
            datasets: [{
                label: 'Hiệu suất',
                data: {!! json_encode($staffPerformanceData) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        }
    });

    // Dữ liệu cho biểu đồ hiệu suất live
    var livePerformanceCtx = document.getElementById('livePerformanceChart').getContext('2d');
    var livePerformanceChart = new Chart(livePerformanceCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($livePerformanceLabels) !!},
            datasets: [
                {
                    label: 'Doanh thu (VND)',
                    data: {!! json_encode($liveRevenueData) !!},
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Giờ live',
                    data: {!! json_encode
::contentReference[oaicite:5]{index=5}
  --}}

