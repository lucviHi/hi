@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">Chênh Lệch Theo Giờ (So sánh Snapshot Hiện Tại - Trước Đó)</h2>
@php
    $totalGmv = $differences->sum('gmv');
    $gmvTarget = $differences->count() * $targetPerHour;
    $percentAchieved = ($gmvTarget > 0) ? round($totalGmv / $gmvTarget * 100, 2) : 0;

    // Chọn class màu tương ứng
    $progressClass = 'bg-danger';
    if ($percentAchieved >= 100) {
        $progressClass = 'bg-success';
    } elseif ($percentAchieved >= 50) {
        $progressClass = 'bg-warning';
    }
@endphp

@if($gmvTarget > 0)
    <div class="mb-4">
        <label class="form-label fw-bold">🎯 Tiến Độ Hoàn Thành GMV:</label>
        <div class="progress" style="height: 25px;">
            <div class="progress-bar {{ $progressClass }}"
                 role="progressbar"
                 style="width: {{ min($percentAchieved, 100) }}%;">
                {{ $percentAchieved }}%
            </div>
        </div>
        <div class="mt-1 small text-muted">
            {{ number_format($totalGmv) }} / {{ number_format($gmvTarget) }}₫
        </div>
    </div>
@endif

        <!-- Bộ lọc ngày và giờ -->
        {{-- <form method="GET" class="d-flex align-items-center gap-3 mb-3">
            
            <label class="form-label mb-0">Ngày:</label>
            <input type="date" name="date" class="form-control" value="{{ $date }}">

            <div class="d-flex gap-2 align-items-center">
                <label class="form-label mb-0">Từ:</label>
                <input type="number" name="hour_from" class="form-control" min="0" max="23" value="{{ $hourFrom }}"
                    style="width: 100px;">

                <label class="form-label mb-0">-đến:</label>
                <input type="number" name="hour_to" class="form-control" min="0" max="23" value="{{ $hourTo }}"
                    style="width: 100px;">
            </div>
            <div class="d-flex gap-2 align-items-center">
            <label class="form-label mb-0">Người:</label>
            <select name="staff_id" class="form-select">
            <option value="">-- Tất cả --</option>
                @foreach ($staffs as $staff)
            <option value="{{ $staff->id }}" {{ request('staff_id') == $staff->id ? 'selected' : '' }}>
                {{ $staff->name }}
            </option>
        @endforeach
    </select>
</div>

            <button class="btn btn-outline-primary">Lọc</button>
        </form> --}}

        <form method="GET" class="row g-2 align-items-end mb-4">
    <!-- Ngày -->
    <div class="col-md-3">
        <label class="form-label fw-semibold">📅 Ngày:</label>
        <input type="date" name="date" class="form-control" value="{{ $date }}">
    </div>

    <!-- Giờ từ -->
    <div class="col-md-2">
        <label class="form-label fw-semibold">Từ giờ:</label>
        <input type="number" name="hour_from" class="form-control" min="0" max="23" value="{{ $hourFrom }}">
    </div>

    <!-- Giờ đến -->
    <div class="col-md-2">
        <label class="form-label fw-semibold">Đến giờ:</label>
        <input type="number" name="hour_to" class="form-control" min="0" max="23" value="{{ $hourTo }}">
    </div>

    <!-- Nhân sự -->
    <div class="col-md-3">
        <label class="form-label fw-semibold">👤 Nhân sự:</label>
        <select name="staff_id" class="form-select">
            <option value="">-- Tất cả --</option>
            @foreach ($staffs as $staff)
                <option value="{{ $staff->id }}" {{ request('staff_id') == $staff->id ? 'selected' : '' }}>
                    {{ $staff->name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Nút lọc -->
    <div class="col-md-2 d-grid">
        <label class="form-label invisible">Lọc</label>
        <button type="submit" class="btn btn-primary">🔍 Lọc</button>
    </div>
</form>


<div class="mb-3">
    <label class="form-label fw-bold">Ẩn/Hiện Cột:</label>
    <div class="d-flex flex-wrap gap-2">
        @php
            $columns = [
                'save' => 'Lưu team',
                'live_impressions' => 'Hiển Thị',
                'views' => 'Lượt Xem',
                'product_clicks' => 'Click SP',
                'items_sold' => 'Sản phẩm bán ra',
                'entry_rate' => 'Vào Phòng',
                'ctr' => 'CTR',
                'ctor' => 'CTOR',
            ];
        @endphp

        @foreach ($columns as $key => $label)
            <div>
                <input type="checkbox" class="toggle-col" data-col="{{ $key }}" checked>
                <label>{{ $label }}</label>
            </div>
        @endforeach
    </div>
</div>

        <div class="table-responsive">
            <table class="table table-bordered text-center align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Giờ</th>
                        <th>Live Chính</th>
                        <th>Trợ Live</th>
                        <th class="col-save">Lưu Team</th>
                        <th>GMV</th>
                        <th>Mục tiêu / giờ</th>
                        <th>% đạt</th>
                        <th>Chi Phí QC</th>
                        <th>ROI khung giờ</th>
                        <th>% Chi Phí QC</th>
                        {{-- <th>Ads Thủ Công</th>
                        <th>Ads Tự Động</th> --}}
                        <th class="col-live_impressions">Hiển Thị</th>
                        <th class="col-views">Lượt Xem</th>
                        <th class="col-product_clicks">Click SP</th>
                        <th>AOV</th>
                        <th class="col-items_sold">Sản phẩm bán ra</th>
                        <th class="col-entry_rate">Vào Phòng</th>
                        <th class="col-ctr">CTR</th>
                        <th class="col-ctor">CTOR</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($differences as $row)
                        <tr>
                            {{-- <td>{{ str_pad($row->hour, 2, '0', STR_PAD_LEFT) }}:00</td> --}}
                        <td class="text-nowrap">
                           {{ str_pad($row->hour-1, 2, '0', STR_PAD_LEFT) }} - {{ str_pad(($row->hour) % 24, 2, '0', STR_PAD_LEFT) }}
                        </td>


                            <form method="POST" action="{{ route('snapshots.assign.hosts') }}">
                                @csrf
                                <input type="hidden" name="room_id" value="{{ $room_id }}">
                                <input type="hidden" name="date" value="{{ $date }}">
                                <input type="hidden" name="hour" value="{{ $row->hour }}">

                                <td>
                                    <select name="main_host_id" class="form-select form-select-sm">
                                        <option value="">-- chọn --</option>
                                        @foreach ($staffs as $staff)
                                            <option value="{{ $staff->id }}" {{ $row->main_host_id == $staff->id ? 'selected' : '' }}>
                                                {{ $staff->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select name="support_host_id" class="form-select form-select-sm">
                                        <option value="">-- chọn --</option>
                                        @foreach ($staffs as $staff)
                                            <option value="{{ $staff->id }}" {{ $row->support_host_id == $staff->id ? 'selected' : '' }}>
                                                {{ $staff->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="col-save" colspan="1">
                                    <button class="btn btn-sm btn-primary">Lưu</button>
                                </td>
                            </form>

                            <td>{{ number_format($row->gmv) }}</td>
                            <td>{{ number_format($row->target_gmv) }}</td>
                            {{-- <td>{{ $row->percent_achieved !== null ? $row->percent_achieved . '%' : '-' }}</td> --}}
                            <td class="{{ $row->percent_achieved >= 100 ? 'bg-success text-white' : 'bg-danger text-white' }}">
                                {{ $row->percent_achieved !== null ? $row->percent_achieved . '%' : '-' }}
                            </td>

                            <td>{{ number_format($row->ads_total_cost) }}</td>
                            {{-- <td>{{ $row->gmv == 0 ? '0%' : round($row->ads_total_cost * 100 / $row->gmv, 2) . '%' }}</td>
                            --}}
                            @php
                                $roi =  $row->ads_total_cost > 0 ? round($row->gmv / $row->ads_total_cost, 2) : 0;
                                $costRate = $row->gmv > 0 ? round($row->ads_total_cost * 100 / $row->gmv, 2) : 0;
                            @endphp
                            <td class="{{ $roi < 8 ? 'bg-danger-subtle text-danger fw-bold' : '' }}">
                                {{ $roi }}
                            </td>
                            <td class="{{ $costRate > 10 ? 'bg-danger-subtle text-danger fw-bold' : '' }}">
                                {{ $costRate . '%' }}
                            </td>

                            {{-- <td>{{ number_format($row->ads_manual_cost) }}</td>
                            <td>{{ number_format($row->ads_auto_cost) }}</td> --}}
                            <td class="col-live_impressions">{{ number_format($row->live_impressions) }}</td>
                            <td class="col-views">{{ number_format($row->views) }}</td>
                            <td class="col-product_clicks">{{ number_format($row->product_clicks) }}</td>
                            <td>{{ ($row->items_sold > 0) ? number_format($row->gmv / $row->items_sold) : '-' }}</td>
                            <td class="col-items_sold">{{ number_format($row->items_sold) }}</td>
                            <td class="col-entry_rate">{{ round($row->entry_rate, 2) . '%'}}</td>
                            <td class="col-ctr">{{ round($row->ctr, 2) . '%' }}</td>
                            <td class="col-ctor">{{ round($row->ctor, 2) . '%'}}</td>
                        </tr>
                    @endforeach
                
                    <tr class="fw-bold bg-light">
                        @php
                        $totalImpressions = $differences->sum('live_impressions');
                        $totalViews = $differences->sum('views');
                        $totalClicks = $differences->sum('product_clicks');
                        $totalItems = $differences->sum('items_sold');

                        $entryRate = $totalImpressions > 0 ? round($totalViews / $totalImpressions * 100, 2) : null;
                        $ctr = $totalViews > 0 ? round($totalClicks / $totalViews * 100, 2) : null;
                        $ctor = $totalClicks > 0 ? round($totalItems / $totalClicks * 100, 2) : null;
                        @endphp

                        <td>Tổng</td>
                        <td id="col-total-team" colspan="3"></td>
                        <td>{{ number_format($differences->sum('gmv')) }}</td>
                        <td>{{ number_format($differences->count() * $targetPerHour) }}</td>
                           @php
                                $diffCount = $differences->count();
                                $percentAchieved = ($targetPerHour > 0 && $diffCount > 0)
                                    ? round($differences->sum('gmv') / ($diffCount * $targetPerHour) * 100, 2)
                                    : null;
                            @endphp
                        <td class="{{ $percentAchieved < 100 ? 'bg-danger-subtle text-danger fw-bold' : 'bg-success text-white' }}">
                            {{ $percentAchieved !== null ? $percentAchieved . '%' : '-' }}
                        </td>

                        <td>{{ number_format($differences->sum('ads_total_cost')) }}</td>
                        @php
                            $roiTotal =$differences->sum('ads_total_cost')> 0 ? round($differences->sum('gmv')/ $differences->sum('ads_total_cost'), 2) : null;
                            $percentCost = $differences->sum('gmv') > 0 ? round($differences->sum('ads_total_cost') * 100 / $differences->sum('gmv'), 2) : null;
                        @endphp
                        <td class="{{ $roiTotal < 8  ? 'bg-danger-subtle text-danger fw-bold' :''}}">
                            {{ $roiTotal !== null ? $roiTotal : '-' }}
                        </td>
                        <td class="{{ $percentCost> 10 ? 'bg-danger-subtle text-danger fw-bold' :''}}">
                            {{ $percentCost !== null ? $percentCost . '%' : '-' }}
                        </td>
                        {{-- <td>{{ number_format($differences->sum('ads_manual_cost')) }}</td>
                        <td>{{ number_format($differences->sum('ads_auto_cost')) }}</td> --}}
                        <td class="col-live_impressions">{{ number_format($differences->sum('live_impressions')) }}</td>
                        <td class="col-views">{{ number_format($differences->sum('views')) }}</td>
                        <td class="col-product_clicks">{{ number_format($differences->sum('product_clicks')) }}</td>
                        <td>{{ ($differences->sum('items_sold') > 0) ? number_format($differences->sum('gmv') / $differences->sum('items_sold')) : '-' }}
                        </td>
                        <td class="col-items_sold">{{ number_format($differences->sum('items_sold')) }}</td>
                        <td class="col-entry_rate">
                           {{ $entryRate !== null ? $entryRate . '%' : '-' }}
                        </td>
                        <td class="col-ctr">
                           {{ $ctr !== null ? $ctr . '%' : '-' }}
                        </td>
                        <td class="col-ctor">
                           {{ $ctor !== null ? $ctor . '%' : '-' }}
                        </td>
                    </tr>

                    

                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const updateColspan = () => {
            const checkbox = document.querySelector('.toggle-col[data-col="save"]');
            const totalTeamCell = document.getElementById('col-total-team');

            if (!checkbox || !totalTeamCell) return;

            const isChecked = checkbox.checked;
            totalTeamCell.colSpan = isChecked ? 3 : 2;
        };

        document.querySelectorAll('.toggle-col').forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                const colClass = 'col-' + this.dataset.col;
                const isChecked = this.checked;

                document.querySelectorAll('.' + colClass).forEach(function (el) {
                    el.style.display = isChecked ? '' : 'none';
                });

                updateColspan();
            });
        });

        // Chạy khi trang load
        updateColspan();
    });
</script>
@endsection


