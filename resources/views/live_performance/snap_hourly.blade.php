
@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">📊 Snapshot tổng theo ngày của tất cả các kênh </h2>

    <!-- Bộ lọc ngày -->
    <form method="GET" class="d-flex align-items-center gap-3 mb-4">
        <label class="form-label mb-0">Chọn ngày:</label>
        <input type="date" name="date" class="form-control" value="{{ $selectedDate }}">
        <button class="btn btn-outline-primary">Lọc</button>
    </form>

    @php
        $totalGMV = 0;
        $totalCost = 0;
        $totalManualCost = 0;
        $totalAutoCost = 0;
        $totalViews = 0;
        $totalImpressions = 0;
        $totalClicks = 0;
        $totalOrders = 0;
    @endphp

    <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
            <thead class="table-light">
                <tr>
                    <th>Room</th>
                    <th>Ngày</th>
                    <th>Giờ</th>
                    <th>GMV</th>
                    <th>Chi phí QC</th>
                    {{-- <th>Ads Manual</th>
                    <th>Ads Auto</th> --}}
                    <th>ROI</th>
                    <th>% QC / GMV</th>
                    <th>Hiển thị</th>
                    <th>View</th>
                    <th>Click SP</th>
                    <th>Đơn hàng</th>
                    <th>Vào phòng</th>
                    <th>AOV sku</th>
                    <th>CTR</th>
                    <th>CTOR</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($snapshot as $item)
                    @php
                        $totalGMV += $item->gmv;
                        $totalCost += $item->ads_total_cost;
                        $totalManualCost += $item->ads_manual_cost;
                        $totalAutoCost += $item->ads_auto_cost;
                        $totalViews += $item->views;
                        $totalImpressions += $item->live_impressions;
                        $totalClicks += $item->product_clicks;
                        $totalOrders += $item->items_sold;
                    @endphp
                    <tr>
                        <td>{{ $item->room->name ?? 'Room #' . $item->room_id }}</td>
                        <td>{{ $item->date }}</td>
                        <td>{{ str_pad($item->hour, 2, '0', STR_PAD_LEFT) }}:00</td>
                        <td>{{ number_format($item->gmv) }}</td>
                        <td>{{ number_format($item->ads_total_cost) }}</td>
                        {{-- <td>{{ number_format($item->ads_manual_cost) }}</td>
                        <td>{{ number_format($item->ads_auto_cost) }}</td> --}}
                        <td>{{ $item->ads_total_cost > 0 ? round($item->gmv / $item->ads_total_cost, 2) : '-' }}</td>
                        <td>{{ $item->gmv > 0 ? round($item->ads_total_cost * 100 / $item->gmv, 2) . '%' : '-' }}</td>
                        <td>{{ number_format($item->live_impressions) }}</td>
                        <td>{{ number_format($item->views) }}</td>
                        <td>{{ number_format($item->product_clicks) }}</td>
                        <td>{{ number_format($item->items_sold) }}</td>
                        <td>{{ $item->live_impressions > 0 ? round($item->views / $item->live_impressions * 100, 2) . '%' : '-' }}</td>
                        <td>{{ $item->items_sold > 0? number_format($item->gmv/ $item->items_sold) : '-' }}</td>
                        <td>{{ $item->views > 0 ? round($item->product_clicks / $item->views * 100, 2) . '%' : '-' }}</td>
                        <td>{{ $item->product_clicks > 0 ? round($item->items_sold / $item->product_clicks * 100, 2) . '%' : '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="16">Không có dữ liệu trong ngày {{ $selectedDate }}.</td>
                    </tr>
                @endforelse

                <!-- Dòng tổng -->
                <tr class="fw-bold bg-light">
                    <td colspan="3">Tổng</td>
                    <td>{{ number_format($totalGMV) }}</td>
                    <td>{{ number_format($totalCost) }}</td>
                    {{-- <td>{{ number_format($totalManualCost) }}</td>
                    <td>{{ number_format($totalAutoCost) }}</td> --}}
                    <td>{{ $totalCost > 0 ? round($totalGMV / $totalCost, 2) : '-' }}</td>
                    <td>{{ $totalGMV > 0 ? round($totalCost * 100 / $totalGMV, 2) . '%' : '-' }}</td>
                    <td>{{ number_format($totalImpressions) }}</td>
                    <td>{{ number_format($totalViews) }}</td>
                    <td>{{ number_format($totalClicks) }}</td>
                    <td>{{ number_format($totalOrders) }}</td>
                    <td>{{ $totalImpressions > 0 ? round($totalViews / $totalImpressions * 100, 2) . '%' : '-' }}</td>
                    <td>{{ $totalOrders > 0 ? number_format($totalGMV/ $totalOrders ): '-' }}</td>
                    <td>{{ $totalViews > 0 ? round($totalClicks / $totalViews * 100, 2) . '%' : '-' }}</td>
                    <td>{{ $totalClicks > 0 ? round($totalOrders / $totalClicks * 100, 2) . '%' : '-' }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
