@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Dòng Chọn Ngày và Nút Import -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quản lý Livestream - Theo Ngày</h2>
        <div class="d-flex align-items-center gap-3">
            <label for="import_date" class="form-label mb-0 me-2">Ngày import:</label>
            <input type="date" id="import_date" name="import_date" class="form-control" value="{{ date('Y-m-d') }}" required>            
            <form action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Chọn file</label>
                    <input type="file" name="file" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-danger text-nowrap">Import Streamer</button>
            </form>
            <form action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Chọn file</label>
                    <input type="file" name="file" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success text-nowrap">Import GMV MAX</button>
            </form>
            <form action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Chọn file</label>
                    <input type="file" name="file" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary text-nowrap">Import Ads Manual</button>
            </form>
        </div>
    </div>
    <hr class="my-4"> 
    <!-- Dòng phân cách -->
    <!-- Bộ lọc khoảng ngày -->
    <form method="GET" class="d-flex align-items-center gap-3 mb-3">
        <label class="form-label mb-0">Từ ngày:</label>
        <input type="date" name="start_date" class="form-control" value="{{ request('start_date', \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d')) }}">
        <label class="form-label mb-0">Đến ngày:</label>
        <input type="date" name="end_date" class="form-control" value="{{ request('end_date', \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d')) }}">
        <button class="btn btn-outline-primary">Lọc</button>
    </form>

    <!-- Bảng dữ liệu theo ngày -->
    <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
            <thead class="table-light">
                <tr>
                    <th>Ngày</th>
                    <th>GMV</th>
                    <th>Chi Phí Quảng Cáo</th>
                    <th>ROAS</th>
                    <th>Đơn Hàng</th>
                    <th>Hiển Thị</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalGMV = 0;
                    $totalCost = 0;
                    $totalOrder = 0;
                    $totalImpression = 0;
                @endphp

                {{-- @foreach ($dailyData as $day => $data) --}}
                    {{-- <tr>
                        <td>{{ $day }}</td>
                        <td>{{ $data['gmv'] }}</td>
                        <td>{{ $data['ads_cost'] }}</td>
                        <td>{{ $data['roas'] }}</td>
                        <td>{{ $data['orders'] }}</td>
                        <td>{{ $data['impressions'] }}</td>
                    </tr>
                    @php
                        $totalGMV += $data['gmv'];
                        $totalCost += $data['ads_cost'];
                        $totalOrder += $data['orders'];
                        $totalImpression += $data['impressions'];
                    @endphp --}}
                {{-- @endforeach --}}

                <!-- Hàng tổng -->
                <tr class="fw-bold bg-light">
                    <td>Tổng</td>
                    <td>{{ $totalGMV }}</td>
                    <td>{{ $totalCost }}</td>
                    <td>{{ $totalCost > 0 ? round($totalGMV / $totalCost, 2) : '-' }}</td>
                    <td>{{ $totalOrder }}</td>
                    <td>{{ $totalImpression }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
