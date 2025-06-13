@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Nhập file theo ngày -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quản lý Livestream - Theo Ngày</h2>
        <div class="d-flex align-items-center gap-3">
            <label for="import_date" class="form-label mb-0 me-2">Ngày import:</label>
            {{-- <input type="date" id="import_date" name="import_date" class="form-control" value="{{ date('Y-m-d') }}" required> --}}

            <!-- Import Streamer -->
            <form action="{{ route('streamer_data_days.import', ['room_id' => $room_id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="daily">
                <div class="mb-3">
                    <label class="form-label">Chọn file</label>
                    <input type="file" name="file" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-danger">Import Streamer</button>
            </form>

            <!-- Import GMV Auto -->
            <form action="{{ route('ads_auto_data_days.import', ['room_id' => $room_id]) }}" method="POST" enctype="multipart/form-data">
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
            <form action="{{ route('ads_manual_data_days.import', ['room_id' => $room_id]) }}" method="POST" enctype="multipart/form-data">
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
                    <th>Chi Phí QC</th>
                    <th>ROAS</th>
                    <th>% Chi phí QC/ GMV</th>
                    <th>Hiển thị</th>
                    <th>Lượt xem</th>
                    <th>Sản phẩm bán</th>
                    <th>Vào phòng</th>
                    <th>CTR</th>
                    <th>CTOR</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalGMV = 0;
                    $totalCost = 0;
                    $totalItems = 0;
                    $totalImpressions = 0;
                    $totalViews = 0;
                    $totalProductClicks = 0;
                    $totalPaidOrders = 0;
                @endphp

                @foreach ($dailyData as $data)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($data->date)->format('d/m/Y') }}</td>
                        <td>{{ number_format($data->gmv) }}</td>
                        <td>{{ number_format($data->ads_total_cost) }}</td>
                        <td>{{ $data->roas_total > 0 ? round($data->gmv/ $data->ads_total_cost, 2) : '-'  }}</td>
                        <td>{{ $data->roas_total > 0 ? round($data->ads_total_cost*100 / $data->gmv, 2) : '-'  }}</td>
                        <td>{{ $data->live_impressions }}</td>
                        <td>{{ $data->views }}</td>
                        <td>{{ $data->items_sold }}</td>
                        <td>{{ $data->entry_rate ? round($data->entry_rate * 100, 2) . '%' : '-' }}</td>
                        <td>{{ $data->ctr ? round($data->ctr * 100, 2) . '%' : '-' }}</td>
                        <td>{{ $data->ctor ? round($data->ctor * 100, 2) . '%' : '-' }}</td>
                    </tr>
                    @php
                        $totalGMV += $data->gmv;
                        $totalCost += $data->ads_total_cost;
                        $totalItems += $data->items_sold;
                        $totalImpressions +=$data->live_impressions;
                        $totalViews += $data->views;
                        $totalProductClicks += $data->product_clicks ?? 0;
                        $totalPaidOrders += $data->items_sold ?? 0; // paid_orders bên streamer là items_sold trong bảng tổng
                    @endphp
                @endforeach

                <tr class="fw-bold bg-light">
                    <td>Tổng</td>
                    <td>{{ number_format($totalGMV) }}</td>
                    <td>{{ number_format($totalCost) }}</td>
                    <td>{{ $totalCost > 0 ? round($totalGMV / $totalCost, 2) : '-' }}</td>
                    <td>{{ $totalGMV > 0 ? round($totalCost*100 / $totalGMV, 2) : '-' }}</td>
                    <td>{{ $totalImpressions }}</td>
                    <td>{{ $totalViews }}</td>
                    <td>{{ $totalItems }}</td>
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
