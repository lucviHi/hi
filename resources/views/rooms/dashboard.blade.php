@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Điều hướng trang quản lý -->
    {{-- <div class="row mb-2">
        <div class="nav nav-pills d-flex align-items-center">
            <a href="{{ route('staff_roles.index', $room->id) }}" class="nav-item nav-link">Quản lý nhân sự</a>
            <span class="mx-2">|</span>
            <a href="{{ route('live_target_days.index', $room->id)}}" class="nav-item nav-link">Quản lý ca trực</a>
            <span class="mx-2">|</span>
            <a href="{{ route('live_performance.daily',$room->id) }}" class="nav-item nav-link">Quản lý Livestream</a>
            <span class="mx-2">|</span>
            <a href="{{ route('live_performance.hourly',$room->id) }}" class="nav-item nav-link">Báo cáo khung giờ</a>
        </div>
    </div> --}}
    <div class="row mb-3">
    <div class="col">
        <div class="nav nav-pills nav-fill bg-white rounded shadow-sm border px-2 py-2 gap-2">
            <a  href="{{ route('staff_roles.index', $room->id) }}"
               class="nav-link fw-semibold  {{ request()->routeIs('staff_roles.*') ? 'active' : 'text-dark' }}">
                 Nhân sự
            </a>
            <a href="{{ route('live_target_days.index', $room->id) }}"
               class="nav-link fw-semibold {{ request()->routeIs('live_target_days.*') ? 'active' : 'text-dark' }}">
                Ca trực
            </a>
            <a href="{{ route('live_performance.daily', $room->id) }}"
               class="nav-link fw-semibold {{ request()->routeIs('live_performance.daily') ? 'active' : 'text-dark' }}">
                Livestream
            </a>
            <a href="{{ route('live_performance.hourly', $room->id) }}"
               class="nav-link fw-semibold {{ request()->routeIs('live_performance.hourly') ? 'active' : 'text-dark' }}">
                Báo cáo khung giờ
            </a>
        </div>
    </div>
</div>

</div>

    <!-- Tiêu đề chính -->
    {{-- <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Bảng Điều Khiển Phòng: {{ $room->name }}</h1>
        </div>
    </div> --}}
<div class="row mb-4 align-items-center">
    <div class="col">
        <h2 class="page-title d-flex align-items-center">
            <i class="fas fa-tv text-primary me-2"></i>
            Bảng Điều Khiển Phòng: <span class="ms-2 fw-semibold text-dark">{{ $room->name }}</span>
        </h2>
    </div>
</div>

<div class="row">
    <!-- Mục tiêu hôm nay -->
    <div class="col-md-6 mb-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="mb-2">🎯 Đạt mục tiêu hôm nay</h6>
                <div class="progress" style="height: 20px;">
                    <div class="progress-bar 
                        {{ $todayTargetPercent >= 100 ? 'bg-success' : ($todayTargetPercent >= 70 ? 'bg-info' : 'bg-danger') }}"
                        role="progressbar"
                        style="width: {{ $todayTargetPercent }}%">
                        {{ $todayTargetPercent }}%
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mục tiêu tháng -->
    <div class="col-md-6 mb-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="mb-2">📆 Đạt mục tiêu tháng này</h6>
                <div class="progress" style="height: 20px;">
                    <div class="progress-bar 
                        {{ $monthTargetPercent >= 100 ? 'bg-success' : ($monthTargetPercent >= 70 ? 'bg-info' : 'bg-danger') }}"
                        role="progressbar"
                        style="width: {{ $monthTargetPercent }}%">
                        {{ $monthTargetPercent }}%
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 7 Chỉ số  --}}
<div class="row">
     <!-- Doanh thu tháng -->
    <div class="col-md-4 mb-3">
        <div class="info-box shadow-sm bg-white">
            <span class="info-box-icon bg-primary"><i class="fas fa-calendar-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Doanh thu tháng</span>
                <span class="info-box-number text-primary">{{ number_format($monthRevenue) }} ₫</span>
            </div>
        </div>
    </div>

    <!-- % Chi phí tháng -->
    <div class="col-md-4 mb-3">
        <div class="info-box shadow-sm bg-white">
            <span class="info-box-icon bg-warning"><i class="fas fa-percentage"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">% Chi phí tháng</span>
                <span class="info-box-number text-warning">{{ $monthCostPercent }}%</span>
            </div>
        </div>
    </div>

    <!-- Doanh thu hôm nay -->
    <div class="col-md-4 mb-3">
        <div class="info-box shadow-sm bg-white">
           
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="info-box shadow-sm bg-white">
            <span class="info-box-icon bg-success"><i class="fas fa-bolt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Doanh thu hôm nay</span>
                <span class="info-box-number text-success">{{ number_format($todayRevenue) }} ₫</span>
            </div>
        </div>
    </div>

    <!-- Doanh thu hôm qua -->
    <div class="col-md-4 mb-3">
        <div class="info-box shadow-sm bg-white">
            <span class="info-box-icon bg-info"><i class="fas fa-history"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Doanh thu hôm qua</span>
                <span class="info-box-number text-info">{{ number_format($yesterdayRevenue) }} ₫</span>
            </div>
        </div>
    </div>

    <!-- Chi phí / Doanh thu hôm nay -->
    <div class="col-md-4 mb-3">
        <div class="info-box shadow-sm bg-white">
            <span class="info-box-icon bg-danger"><i class="fas fa-wallet"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">% Chi phí hôm nay</span>
                <span class="info-box-number text-danger">{{ $todayCostPercent }}%</span>
            </div>
        </div>
    </div>

   
</div>




<div class="container">
    <h4 class="mb-4">📊 GMV theo giờ - {{ $room->name }}</h4>
    <canvas id="gmvChart"></canvas>
</div>

<div class="row">
    <!-- Hôm nay -->
    <div class="col-md-6">
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-white border-0 d-flex align-items-center">
                <i class="fas fa-crown text-warning me-2"></i>
                <strong>Top 3 Host chính hôm nay</strong>
            </div>
            <div class="card-body p-0">
                @if (count($topMainHostsToday))
                    <ul class="list-group list-group-flush">
                        @foreach ($topMainHostsToday as $index => $host)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    {{ $index + 1 }}. {{ $host['name'] }}
                                </span>
                                <span class="badge bg-primary">
                                    {{ number_format($host['gmv']) }} ₫
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="p-3 text-muted fst-italic">Chưa có dữ liệu hôm nay.</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Tháng này -->
    <div class="col-md-6">
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-white border-0 d-flex align-items-center">
                <i class="fas fa-calendar-alt text-success me-2"></i>
                <strong>Top 3 Host chính tháng này</strong>
            </div>
            <div class="card-body p-0">
                @if (count($topMainHostsMonth))
                    <ul class="list-group list-group-flush">
                        @foreach ($topMainHostsMonth as $index => $host)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    {{ $index + 1 }}. {{ $host['name'] }}
                                </span>
                                <span class="badge bg-success">
                                    {{ number_format($host['gmv']) }} ₫
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="p-3 text-muted fst-italic">Chưa có dữ liệu tháng này.</div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- GMV Host chính hôm nay -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-semibold">
                📊 GMV các Host chính hôm nay
            </div>
            <div class="card-body">
                <canvas id="chartHostsToday" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- GMV Host chính tháng -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-semibold">
                📅 GMV các Host chính tháng này
            </div>
            <div class="card-body">
                <canvas id="chartHostsMonth" height="200"></canvas>
            </div>
        </div>
    </div>
</div>
   {{-- 📊 GMV và Chi phí tuần này --}}
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-semibold">📆 GMV & Chi phí tuần này</div>
                <div class="card-body">
                    <canvas id="chartWeeklyRoom" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-semibold">📅 GMV & Chi phí tháng này</div>
                <div class="card-body">
                    <canvas id="chartMonthlyRoom" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const allHours = {!! json_encode($allHours) !!};
const todayRaw = @json($todayData);
const yesterdayRaw = @json($yesterdayData);

// Lấp đầy 24 giờ
function fillData(hourList, rawData) {
    return hourList.map(h => rawData[h] ?? 0);
}

const ctx = document.getElementById('gmvChart').getContext('2d');
const chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: allHours,
        datasets: [
            {
                label: 'Hôm Nay',
                data: fillData(allHours, todayRaw),
                borderColor: 'blue',
                backgroundColor: 'rgba(0, 0, 255, 0.08)',
                fill: true,
                tension: 0.4,
                borderWidth: 2,
                pointRadius: 2,
                pointHoverRadius: 4,
                spanGaps: true
            },
            {
                label: 'Hôm Qua',
                data: fillData(allHours, yesterdayRaw),
                borderColor: 'orange',
                backgroundColor: 'rgba(255, 165, 0, 0.08)',
                fill: true,
                borderDash: [4, 4],
                tension: 0.4,
                borderWidth: 2,
                pointRadius: 2,
                pointHoverRadius: 4,
                spanGaps: true
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            },
            tooltip: {
                mode: 'index',
                intersect: false
            }
        },
        interaction: {
            mode: 'nearest',
            axis: 'x',
            intersect: false
        },
        scales: {
            y: {
                beginAtZero: true,
                title: { display: true, text: 'GMV (₫)' }
            },
            x: {
                title: { display: true, text: 'Giờ' }
            }
        }
    }
});


  // Dữ liệu từ controller
    const labelsToday = {!! json_encode(collect($topMainHostsToday)->pluck('name')) !!};
    const dataToday = {!! json_encode(collect($topMainHostsToday)->pluck('gmv')->map(fn($v) => max(0, $v))) !!};

    const labelsMonth = {!! json_encode(collect($topMainHostsMonth)->pluck('name')) !!};
    const dataMonth = {!! json_encode(collect($topMainHostsMonth)->pluck('gmv')->map(fn($v) => max(0, $v))) !!};

    // Vẽ biểu đồ nếu có dữ liệu
    if (labelsToday.length > 0) {
        new Chart(document.getElementById('chartHostsToday'), {
            type: 'bar',
            data: {
                labels: labelsToday,
                datasets: [{
                    label: 'GMV hôm nay',
                    data: dataToday,
                    backgroundColor: 'rgba(0, 123, 255, 0.6)',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'GMV (₫)' }
                    },
                    x: {
                        ticks: { autoSkip: false },
                        categoryPercentage: 0.6,
                        barPercentage: 0.6
                    }
                },
                plugins: { legend: { display: false } }
            }
        });
    } else {
        document.getElementById('chartHostsToday').replaceWith(
            `Không có dữ liệu host chính hôm nay.`
        );
    }

    if (labelsMonth.length > 0) {
        new Chart(document.getElementById('chartHostsMonth'), {
            type: 'bar',
            data: {
                labels: labelsMonth,
                datasets: [{
                    label: 'GMV tháng này',
                    data: dataMonth,
                    backgroundColor: 'rgba(40, 167, 69, 0.6)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'GMV (₫)' }
                    },
                    x: {
                        ticks: { autoSkip: false },
                        categoryPercentage: 0.6,
                        barPercentage: 0.6
                    }
                },
                plugins: { legend: { display: false } }
            }
        });
    } else {
        document.getElementById('chartHostsMonth').replaceWith(
            `Không có dữ liệu host chính trong tháng.`
        );
    }

    function renderDualChart(canvasId, data, labelBar, labelLine) {
    const labels = data.map(d => d.date);
    const gmvData = data.map(d => d.gmv);
    const costRateData = data.map(d => d.cost_percent);

    new Chart(document.getElementById(canvasId), {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    type: 'bar',
                    label: labelBar,
                    data: gmvData,
                    backgroundColor: 'rgba(0,123,255,0.6)',
                    borderColor: 'rgba(0,123,255,1)',
                    borderWidth: 1,
                    yAxisID: 'y'
                },
                {
                    type: 'line',
                    label: labelLine,
                    data: costRateData,
                    borderColor: 'rgba(255,99,132,1)',
                    backgroundColor: 'rgba(255,99,132,0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'GMV (₫)' }
                },
                y1: {
                    beginAtZero: true,
                    position: 'right',
                    title: { display: true, text: '% Chi phí / GMV' },
                    grid: { drawOnChartArea: false }
                }
            },
            plugins: {
                tooltip: {
                    mode: 'index',
                    intersect: false
                },
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

// Render charts
renderDualChart('chartWeeklyRoom', {!! json_encode($weeklyStats) !!}, 'GMV Tuần này', '% Chi phí/GMV');
renderDualChart('chartMonthlyRoom', {!! json_encode($monthlyStats) !!}, 'GMV Tháng này', '% Chi phí/GMV');
</script>

@endsection