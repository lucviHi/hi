@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Nhập file theo ngày --> 
    <h2 class="mb-3">Quản lý Livestream - Theo Ngày </h2>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex gap-4 align-items-end" style="max-width: 980px;">
            <!-- Import Streamer -->
            <form action="{{ route('streamer_data_days.import', ['room_id' => $room_id]) }}" method="POST" enctype="multipart/form-data" class="d-flex flex-column justify-content-between" >
                @csrf
                <input type="hidden" name="type" value="daily">
                <div class="mb-3">
                    <label class="form-label">Chọn file</label>
                    <input type="file" name="file" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-danger">Import Streamer</button>
            </form>

            <!-- Import GMV Auto -->
            <form action="{{ route('ads_auto_data_days.import', ['room_id' => $room_id]) }}" method="POST" enctype="multipart/form-data" class="d-flex flex-column justify-content-between" >
                @csrf
                <input type="hidden" name="type" value="daily">

                <label class="form-label mb-0">Ngày import:</label>
                <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>

                <div class="mb-3">
                    <label class="form-label">Chọn file</label>
                    <input type="file" name="file" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success">Import GMV MAX</button>
            </form>

            <!-- Import Ads Manual -->
            <form action="{{ route('ads_manual_data_days.import', ['room_id' => $room_id]) }}" method="POST" enctype="multipart/form-data" class="d-flex flex-column justify-content-between" >
                @csrf
                <input type="hidden" name="type" value="daily">
                <div class="mb-3">
                    <label class="form-label">Chọn file</label>
                    <input type="file" name="file" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Import Ads Manual</button>
            </form>
        </div>
    </div>

    <hr class="my-4">

    <!-- Bộ lọc ngày -->
    <form method="GET" class="d-flex align-items-center gap-3 mb-3">
        <label class="form-label mb-0">Từ ngày:</label>
        <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
        <label class="form-label mb-0">Đến ngày:</label>
        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
        <button class="btn btn-outline-primary">Lọc</button>
    </form>

    <!-- Bảng dữ liệu -->
    <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
            <thead class="table-light">
                <tr>
                    <th>Ngày</th>
                    <th>GMV</th>
                    <th>Tổng Chi Phí</th>
                    <th>%Tổng Chi Phí/ GMV</th>
                    <th>Chi phí Deal</th>
                    <th>Lưu Chi phí Deal</th>
                    <th>Chi Phí QC</th>
                    <th>Chi Phí Ads thủ công</th>
                    <th>Chi Phí Ads tự động</th>
                    <th>ROI</th>
                    <th>% Chi phí QC/ GMV</th>
                    <th>Hiển thị</th>
                    <th>Lượt xem</th>
                    <th>Lượt Click sản phẩm</th>
                    <th>Sản phẩm bán</th>
                    <th>Vào phòng</th>
                    <th>CTR</th>
                    <th>CTOR</th>
                    <th>Xóa</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalGMV = 0;
                    $totalCost = 0;
                    $totalManualCost = 0;
                    $totalAutoCost = 0;
                    $totalItems = 0;
                    $totalImpressions = 0;
                    $totalClicks = 0;
                    $totalViews = 0;
                    $totalProductClicks = 0;
                    $totalPaidOrders = 0;
                    $totalDeal = 0;
                    $total = 0;
                @endphp
                @foreach ($dailyData as $data)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($data->date)->format('d/m/Y') }}</td>
                        <td>{{ number_format($data->gmv) }}</td>
                        <td>{{ number_format(($data->ads_total_cost ?? 0) + ($data->deal_cost ?? 0)) }}</td>
                        <td>
                            {{ $data->gmv > 0 ? round((($data->ads_total_cost ?? 0) + ($data->deal_cost ?? 0)) * 100 / $data->gmv, 2) . '%' : '-' }}
                        </td>
                        <td>
                <form method="POST" action="{{ route('live-performance.update-deal-cost') }}" class="d-flex">
                            @csrf
                            <input type="hidden" name="id" value="{{ $data->id }}">
                           <input type="text" name="deal_cost" value="{{ number_format($data->deal_cost ?? 0) }}" class="form-control form-control-sm text-end" style="width: 100px;">

                        </td>

                        {{-- Cột nút Lưu --}}
                        <td>
                            <button class="btn btn-sm btn-outline-primary" title="Lưu">
                               Lưu
                            </button>
                </form>
                        </td>

                        <td>{{ number_format($data->ads_total_cost) }}</td>
                        <td>{{ number_format($data->ads_manual_cost) }}</td>
                        <td>{{ number_format($data->ads_auto_cost) }}</td>
                        <td>{{ $data->ads_total_cost > 0 ? round($data->gmv/ $data->ads_total_cost, 2) : '-'  }}</td>
                        <td>{{ $data->gmv > 0 ? round($data->ads_total_cost*100 / $data->gmv, 2). '%' : '-'  }}</td>
                        <td>{{ number_format($data->live_impressions) }}</td>
                        <td>{{ number_format($data->views) }}</td>
                        <td>{{ number_format($data->product_clicks) }}</td>
                        <td>{{ number_format($data->items_sold) }}</td>
                        <td>{{ $data->live_impressions > 0? round($data->views/ $data->live_impressions*100, 2) . '%' : '-' }}</td>
                        <td>{{ $data->views > 0 ? round($data->product_clicks/$data->views * 100, 2) . '%' : '-' }}</td>
                        <td>{{ $data->product_clicks > 0 ? round($data->items_sold/ $data->product_clicks * 100, 2) . '%' : '-' }}</td>
                        <td>
                            <form action="{{ route('live_performance_days.destroy', ['id' => $data->id]) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xoá dòng dữ liệu giờ {{ $data->hour }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Xoá</button>
                            </form>
                        </td>
                    </tr>
                    @php
                        $totalGMV += $data->gmv;
                        $totalCost += $data->ads_total_cost;
                        $totalManualCost += $data->ads_manual_cost;
                        $totalAutoCost += $data->ads_auto_cost;
                        $totalItems += $data->items_sold;
                        $totalClicks += $data->product_clicks;
                        $totalImpressions +=$data->live_impressions;
                        $totalViews += $data->views;
                        $totalProductClicks += $data->product_clicks;
                        $totalPaidOrders += $data->items_sold; // paid_orders bên streamer là items_sold trong bảng tổng
                        $totalDeal += $data->deal_cost; 
                        $total += $data->total_cost; 
                    @endphp
                @endforeach
              
                <tr class="fw-bold bg-light">
                    <td>Tổng</td>
                    <td>{{ number_format($totalGMV) }}</td>
                    <td>{{ number_format($total) }}</td>
                    <td>{{ $totalGMV> 0 ? round($total *100/ $totalGMV, 2) . '%' : '-' }}</td>
                    <td>{{ number_format($totalDeal) }}</td>
                    <td></td>
                    <td>{{ number_format($totalCost) }}</td>
                    <td>{{ number_format($totalManualCost) }}</td>
                    <td>{{ number_format($totalAutoCost) }}</td>
                    <td>{{ $totalCost > 0 ? round($totalGMV / $totalCost, 2)  : '-' }}</td>
                    <td>{{ $totalGMV > 0 ? round($totalCost*100 / $totalGMV, 2) . '%' : '-' }}</td>
                    <td>{{ number_format($totalImpressions) }}</td>
                    <td>{{ number_format($totalViews) }}</td>
                    <td>{{ number_format($totalClicks) }}</td>
                    <td>{{ number_format($totalItems) }}</td>
                    <td>
                        {{ $totalImpressions > 0 ? round(($totalViews / $totalImpressions) * 100, 2) . '%' : '-' }}
                    </td>
                    <td>
                        {{ $totalViews > 0 ? round(($totalProductClicks / $totalViews) * 100, 2) . '%' : '-' }}
                    </td>
                    <td>
                        {{ $totalProductClicks > 0 ? round(($totalPaidOrders / $totalProductClicks) * 100, 2) . '%' : '-' }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
