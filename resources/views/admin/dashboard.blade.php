@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- 🔽 Dropdown lọc Project & Room --}}
    <form method="GET" class="mb-4">
        <div class="row g-2 align-items-center">
            <div class="col-auto fw-bold">📂 Dự án:</div>
            <div class="col-md-4">
                <select name="project_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Tất cả --</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}" {{ $selectedProjectId == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto fw-bold">🗂 Kênh:</div>
            <div class="col-md-3">
                <select name="room_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Tất cả --</option>
                    @foreach ($rooms as $room)
                        <option value="{{ $room->id }}" {{ $selectedRoomId == $room->id ? 'selected' : '' }}>{{ $room->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>

    {{-- Tiến độ mục tiêu --}}
    <div class="row mb-3">
        @foreach ([['🎯 Đạt mục tiêu hôm nay', $todayTargetPercent], ['📆 Đạt mục tiêu tháng này', $monthTargetPercent]] as [$label, $value])
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="mb-2">{{ $label }}</h6>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar {{ $value >= 100 ? 'bg-success' : ($value >= 70 ? 'bg-warning' : 'bg-danger') }}" style="width: {{ $value }}%">{{ $value }}%</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- 7 chỉ số chính --}}
    <div class="row mb-3">
        @foreach ([
            ['Doanh thu hôm nay', $todayRevenue, 'success', 'bolt'],
            ['Doanh thu hôm qua', $yesterdayRevenue, 'info', 'history'],
            ['% Chi phí hôm nay', $todayCostPercent . '%', 'danger', 'wallet'],
            ['Doanh thu tháng', $monthRevenue, 'primary', 'calendar-alt'],
            ['% Chi phí tháng', $monthCostPercent . '%', 'warning', 'percent']
        ] as [$title, $value, $color, $icon])
            <div class="col-md-4 mb-3">
                <div class="info-box bg-white border shadow-sm">
                    <span class="info-box-icon bg-{{ $color }} text-white"><i class="fas fa-{{ $icon }}"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ $title }}</span>
                        <span class="info-box-number text-{{ $color }}">{{ is_numeric($value) ? number_format($value) : $value }} {!! is_numeric($value) && !str_contains($title, '%') ? '₫' : '' !!}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Biểu đồ GMV theo giờ --}}
    <div class="card mb-4">
        <div class="card-header fw-semibold">⏰ Biểu đồ GMV theo giờ hôm nay</div>
        <div class="card-body">
            <canvas id="gmvByHourChart" height="120"></canvas>
        </div>
    </div>
  {{-- Row: Top Host hôm nay & tháng --}}
    <div class="row mb-4">
        @foreach ([
            ['👑 Top Host chính hôm nay', $topMainHostsToday, 'primary'],
            ['📅 Top Host chính tháng này', $topMainHostsMonth, 'success']
        ] as [$title, $hosts, $color])
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header fw-semibold">{{ $title }}</div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach ($hosts as $i => $host)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                @if ($i == 0) 🥇
                                @elseif ($i == 1) 🥈
                                @elseif ($i == 2) 🥉
                                @else {{ $i + 1 }}.
                                @endif
                                {{ $host['name'] }}
                            </span>
                            <span class="badge bg-{{ $color }}">{{ number_format($host['gmv']) }} ₫</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Row: Xếp hạng GMV kênh hôm nay & tháng --}}
    <div class="row mb-4">
        @foreach ([
            ['🏆 Xếp hạng kênh hôm nay', $gmvByRoomToday, 'primary'],
            ['📊 Xếp hạng kênh tháng', $gmvByRoomMonth, 'info']
        ] as [$title, $list, $color])
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header fw-semibold">{{ $title }}</div>
                <div class="card-body p-0">
                    @if ($list->count())
                        <ul class="list-group list-group-flush">
                            @foreach ($list as $index => $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>
                                        @if ($index == 0) 🥇
                                        @elseif ($index == 1) 🥈
                                        @elseif ($index == 2) 🥉
                                        @else {{ $index + 1 }}.
                                        @endif
                                        {{ $item['room'] }}
                                    </span>
                                    <span class="badge bg-{{ $color }}">{{ number_format($item['gmv']) }} ₫</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="p-3 text-muted fst-italic">Chưa có dữ liệu.</div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    {{-- 🔝 Xếp hạng GMV theo Project --}}
<div class="row mb-4">
    @foreach ([['🏆 Xếp hạng dự án hôm nay', $gmvByProjectToday, 'warning'], ['📆 Xếp hạng dự án tháng này', $gmvByProjectMonth, 'info']] as [$title, $list, $color])
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header fw-semibold">{{ $title }}</div>
                <div class="card-body p-0">
                    @if ($list->count())
                        <ul class="list-group list-group-flush">
                            @foreach ($list as $index => $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>
                                        @if ($index == 0) 🥇
                                        @elseif ($index == 1) 🥈
                                        @elseif ($index == 2) 🥉
                                        @else {{ $index + 1 }}.
                                        @endif
                                        {{ $item['project'] }}
                                    </span>
                                    <span class="badge bg-{{ $color }}">{{ number_format($item['gmv']) }} ₫</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="p-3 text-muted fst-italic">Chưa có dữ liệu.</div>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
{{-- ⚠️ Danh sách kênh vượt 8% chi phí ads --}}
<div class="row mb-4">
    @foreach ([['📊 Vượt chi phí hôm nay', $overspendRoomsToday, 'danger'], ['🗓 Vượt chi phí tháng này', $overspendRoomsMonth, 'warning']] as [$title, $rooms, $color])
    <div class="col-md-6">
        <div class="card border-{{ $color }}">
            <div class="card-header text-{{ $color }} fw-semibold">
                {{ $title }}
            </div>
            <div class="card-body p-0">
                @if ($rooms->count())
                <ul class="list-group list-group-flush">
                    @foreach ($rooms as $r)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $r['room'] }}</strong> <small class="text-muted">({{ $r['project'] }})</small>
                        </div>
                        <span class="badge bg-{{ $color }}">{{ $r['cost_percent'] }}%</span>
                    </li>
                    @endforeach
                </ul>
                @else
                <div class="p-3 text-muted fst-italic">Không có kênh nào vượt 8%.</div>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

    {{-- Biểu đồ so sánh GMV theo kênh (room) và dự án (project) --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header fw-semibold">🏆 GMV hôm nay theo kênh</div>
                <div class="card-body">
                    <canvas id="gmvByRoomTodayChart" height="120"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header fw-semibold">📊 GMV hôm nay theo dự án</div>
                <div class="card-body">
                    <canvas id="gmvByProjectTodayChart" height="120"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mt-4">
            <div class="card">
                <div class="card-header fw-semibold">🏅 GMV tháng này theo kênh</div>
                <div class="card-body">
                    <canvas id="gmvByRoomMonthChart" height="120"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mt-4">
            <div class="card">
                <div class="card-header fw-semibold">📈 GMV tháng này theo dự án</div>
                <div class="card-body">
                    <canvas id="gmvByProjectMonthChart" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>
 {{-- Biểu đồ Chi phí theo kênh và dự án --}}
<div class="row mb-4">
    {{-- 🧾 Chi phí hôm nay theo kênh --}}
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header fw-semibold">🧾 Chi phí hôm nay theo kênh</div>
            <div class="card-body">
                <canvas id="costByRoomTodayChart" height="100"></canvas>
            </div>
        </div>
    </div>

    {{-- 🧾 Chi phí tháng theo kênh --}}
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header fw-semibold">🧾 Chi phí tháng theo kênh</div>
            <div class="card-body">
                <canvas id="costByRoomMonthChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    {{-- 🧾 Chi phí hôm nay theo dự án --}}
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header fw-semibold">💰 Chi phí hôm nay theo dự án</div>
            <div class="card-body">
                <canvas id="costByProjectTodayChart" height="100"></canvas>
            </div>
        </div>
    </div>

    {{-- 🧾 Chi phí tháng theo dự án --}}
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header fw-semibold">💰 Chi phí tháng theo dự án</div>
            <div class="card-body">
                <canvas id="costByProjectMonthChart" height="100"></canvas>
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
    const todayData = {!! json_encode(array_values($todayData->toArray())) !!};
    const yesterdayData = {!! json_encode(array_values($yesterdayData->toArray())) !!};

    // new Chart(document.getElementById('gmvByHourChart'), {
    //     type: 'line',
    //     data: {
    //         labels: allHours,
    //         datasets: [
    //             {
    //                 label: 'Hôm nay',
    //                 data: todayData,
    //                 borderColor: 'blue',
    //                 backgroundColor: 'rgba(0,123,255,0.1)',
    //                 fill: true,
    //                 tension: 0.4
    //             },
    //             {
    //                 label: 'Hôm qua',
    //                 data: yesterdayData,
    //                 borderColor: 'orange',
    //                 backgroundColor: 'rgba(255,165,0,0.1)',
    //                 fill: true,
    //                 borderDash: [4, 4],
    //                 tension: 0.4
    //             }
    //         ]
    //     },
    //     options: {
    //         responsive: true,
    //         scales: { y: { beginAtZero: true } }
    //     }
    // });
new Chart(document.getElementById('gmvByHourChart'), {
    type: 'line',
    data: {
        labels: allHours,
        datasets: [
            {
                label: 'Hôm nay',
                data: todayData,
                borderColor: 'blue',
                backgroundColor: 'rgba(0,123,255,0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointHoverRadius: 5
            },
            {
                label: 'Hôm qua',
                data: yesterdayData,
                borderColor: 'orange',
                backgroundColor: 'rgba(255,165,0,0.1)',
                fill: true,
                borderDash: [4, 4],
                tension: 0.4,
                pointRadius: 3,
                pointHoverRadius: 5
            }
        ]
    },
    options: {
        responsive: true,
        interaction: {
            mode: 'index',
            intersect: false
        },
        plugins: {
            tooltip: {
                mode: 'index',
                intersect: false,
                callbacks: {
                    label: function(context) {
                        const value = context.parsed.y || 0;
                        return context.dataset.label + ': ' + value.toLocaleString() + ' ₫';
                    }
                }
            },
            legend: {
                position: 'bottom'
            }
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

    function renderBarChartCurrency(id, labels, data, labelText, color) {
    new Chart(document.getElementById(id), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: labelText,
                data: data,
                backgroundColor: color,
                borderColor: color.replace('0.6', '1'),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: '₫' },
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('vi-VN') + ' ₫';
                        }
                    }
                },
                x: { ticks: { autoSkip: false } }
            }
        }
    });
}

function renderBarChartPercent(id, labels, data, labelText, color) {
    new Chart(document.getElementById(id), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: labelText,
                data: data,
                backgroundColor: color,
                borderColor: color.replace('0.6', '1'),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: '%' },
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                },
                x: { ticks: { autoSkip: false } }
            }
        }
    });
}

 // GMV Charts (₫)
renderBarChartCurrency('gmvByRoomTodayChart', {!! json_encode($gmvByRoomToday->pluck('room')) !!}, {!! json_encode($gmvByRoomToday->pluck('gmv')) !!}, 'GMV hôm nay', 'rgba(54, 162, 235, 0.6)');
renderBarChartCurrency('gmvByRoomMonthChart', {!! json_encode($gmvByRoomMonth->pluck('room')) !!}, {!! json_encode($gmvByRoomMonth->pluck('gmv')) !!}, 'GMV tháng này', 'rgba(75, 192, 192, 0.6)');
renderBarChartCurrency('gmvByProjectTodayChart', {!! json_encode($gmvByProjectToday->pluck('project')) !!}, {!! json_encode($gmvByProjectToday->pluck('gmv')) !!}, 'GMV hôm nay', 'rgba(255, 206, 86, 0.6)');
renderBarChartCurrency('gmvByProjectMonthChart', {!! json_encode($gmvByProjectMonth->pluck('project')) !!}, {!! json_encode($gmvByProjectMonth->pluck('gmv')) !!}, 'GMV tháng này', 'rgba(153, 102, 255, 0.6)');

// Cost Charts (%)
renderBarChartPercent('costByRoomTodayChart', {!! json_encode($costByRoomToday->pluck('room')) !!}, {!! json_encode($costByRoomToday->pluck('cost_percent')) !!}, 'Chi phí hôm nay (%)', 'rgba(255, 99, 132, 0.6)');
renderBarChartPercent('costByRoomMonthChart', {!! json_encode($costByRoomMonth->pluck('room')) !!}, {!! json_encode($costByRoomMonth->pluck('cost_percent')) !!}, 'Chi phí tháng này (%)', 'rgba(255, 159, 64, 0.6)');
renderBarChartPercent('costByProjectTodayChart', {!! json_encode($costByProjectToday->pluck('project')) !!}, {!! json_encode($costByProjectToday->pluck('cost_percent')) !!}, 'Chi phí hôm nay (%)', 'rgba(54, 162, 235, 0.6)');
renderBarChartPercent('costByProjectMonthChart', {!! json_encode($costByProjectMonth->pluck('project')) !!}, {!! json_encode($costByProjectMonth->pluck('cost_percent')) !!}, 'Chi phí tháng này (%)', 'rgba(255, 206, 86, 0.6)');

</script>
@endsection
