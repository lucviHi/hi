@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Nhập file theo khung giờ -->
   <!-- Hàng trên: chọn giờ dùng chung -->
<div class="row mb-3 align-items-end">
    <div class="col-md-3">
        <label class="form-label">Khung giờ:</label>
        <select id="shared-hour" class="form-select">
            @for ($i = 0; $i <= 23; $i++)
                <option value="{{ $i }}" {{ $i == now()->setTimezone('Asia/Ho_Chi_Minh')->hour - 1 ? 'selected' : '' }}>
                    {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}:00
                </option>
            @endfor
        </select>
    </div>
</div>

<!-- Hàng dưới: 3 form nằm ngang -->
<div class="row">
    <!-- Streamer -->
    <div class="col-md-4">
        <form action="{{ route('streamer_data_days.import', ['room_id' => $room_id]) }}" method="POST" enctype="multipart/form-data" class="import-form">
            @csrf
            <input type="hidden" name="type" value="hourly">
            <input type="hidden" name="hour" class="form-hour">

            <label class="form-label">Streamer</label>
            <input type="file" name="file" class="form-control mb-2" required>
            <button type="submit" class="btn btn-danger w-100">Import</button>
        </form>
    </div>

    <!-- GMV Auto -->
    <div class="col-md-4">
        <form action="{{ route('ads_auto_data_days.import', ['room_id' => $room_id]) }}" method="POST" enctype="multipart/form-data" class="import-form">
            @csrf
            <input type="hidden" name="type" value="hourly">
            <input type="hidden" name="hour" class="form-hour">

            <label class="form-label">Ngày:</label>
            <input type="date" name="date" class="form-control mb-2" value="{{ date('Y-m-d') }}" required>

            <label class="form-label">GMV MAX</label>
            <input type="file" name="file" class="form-control mb-2" required>
            <button type="submit" class="btn btn-success w-100">Import</button>
        </form>
    </div>

    <!-- Ads Manual -->
    <div class="col-md-4">
        <form action="{{ route('ads_manual_data_days.import', ['room_id' => $room_id]) }}" method="POST" enctype="multipart/form-data" class="import-form">
            @csrf
            <input type="hidden" name="type" value="hourly">
            <input type="hidden" name="hour" class="form-hour">

            <label class="form-label">Ads Manual</label>
            <input type="file" name="file" class="form-control mb-2" required>
            <button type="submit" class="btn btn-primary w-100">Import</button>
        </form>
    </div>
</div>

    <hr class="my-4">

    <!-- Bộ lọc ngày và giờ -->
    <form method="GET" class="d-flex align-items-center gap-3 mb-3">
        <label class="form-label mb-0">Ngày:</label>
        <input type="date" name="date" class="form-control" value="{{ $date }}">
        <div class="d-flex gap-2 align-items-center">
            <label class="form-label mb-0">Từ giờ:</label>
            <input
                type="number"
                name="hour_from"
                class="form-control"
                placeholder="0"
                min="0"
                max="23"
                value="{{ request('hour_from') }}"
                style="width: 100px;"
            >
        
            <label class="form-label mb-0">đến:</label>
            <input
                type="number"
                name="hour_to"
                class="form-control"
                placeholder="23"
                min="0"
                max="23"
                value="{{ request('hour_to') }}"
                style="width: 100px;"
            >
        </div>
        
    

        <button class="btn btn-outline-primary">Lọc</button>
    </form>

    <!-- Bảng dữ liệu -->
    <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
            <thead class="table-light">
                <tr>
                    <th>Giờ</th>
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
                    $totalViews = 0;
                    $totalImpressions = 0;
                    $totalProductClicks = 0;
                    $totalPaidOrders = 0;
                @endphp

                @foreach ($hourlyData as $data)
                    <tr>
                        <td>{{ $data->hour }}</td>
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
                        $totalViews += $data->views;
                        $totalImpressions += $data->live_impressions;
                        $totalProductClicks += $data->product_clicks ?? 0;
                        $totalPaidOrders += $data->items_sold ?? 0; // paid_orders bên streamer là items_sold trong bảng tổng
                    @endphp
                @endforeach

                <tr class="fw-bold bg-light">
                    <td>Tổng</td>
                    <td>{{ number_format($totalGMV) }}</td>
                    <td>{{ number_format($totalCost) }}</td>
                    <td>{{ $totalCost > 0 ? round($totalGMV / $totalCost, 2) : '-' }}</td>
                    <td>{{ $totalGMV  > 0 ? round($totalCost *100 / $totalGMV, 2) : '-' }}</td>
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
@section('scripts')
<script>
    const sharedHour = document.getElementById('shared-hour');

    document.querySelectorAll('.import-form').forEach(form => {
        form.addEventListener('submit', () => {
            form.querySelector('.form-hour').value = parseInt(sharedHour.value);
        });
    });
</script>
@endsection

