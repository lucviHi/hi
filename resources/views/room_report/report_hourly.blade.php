@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Chênh Lệch Theo Giờ (So sánh Snapshot Hiện Tại - Trước Đó)</h2>

    <!-- Bộ lọc ngày và giờ -->
    <form method="GET" class="d-flex align-items-center gap-3 mb-3">
        <label class="form-label mb-0">Ngày:</label>
        <input type="date" name="date" class="form-control" value="{{ $date }}">

        <div class="d-flex gap-2 align-items-center">
            <label class="form-label mb-0">Từ:</label>
            <input type="number" name="hour_from" class="form-control" min="0" max="23" value="{{ $hourFrom }}" style="width: 100px;">

            <label class="form-label mb-0">-đến:</label>
            <input type="number" name="hour_to" class="form-control" min="0" max="23" value="{{ $hourTo }}" style="width: 100px;">
        </div>

        <button class="btn btn-outline-primary">Lọc</button>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
            <thead class="table-light">
                <tr>
                    <th>Giờ</th>
                    <th>GMV</th>
                    <th>Mục tiêu / giờ</th>
                    <th>% đạt</th>
                    <th>Chi Phí QC</th>
                    <th>Ads Thủ Công</th>
                    <th>Ads Tự Động</th>
                    <th>Hiển Thị</th>
                    <th>Lượt Xem</th>
                    <th>Click SP</th>
                    <th>Bán</th>
                    <th>Vào Phòng</th>
                    <th>CTR</th>
                    <th>CTOR</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($differences as $row)
                <tr>
                    <td>{{ str_pad($row->hour, 2, '0', STR_PAD_LEFT) }}:00</td>
                    <td>{{ number_format($row->gmv) }}</td>
                    <td>{{ number_format($row->target_gmv) }}</td>
                    <td>{{ $row->percent_achieved !== null ? $row->percent_achieved . '%' : '-' }}</td>
                    <td>{{ number_format($row->ads_total_cost) }}</td>
                    <td>{{ number_format($row->ads_manual_cost) }}</td>
                    <td>{{ number_format($row->ads_auto_cost) }}</td>
                    <td>{{ number_format($row->live_impressions) }}</td>
                    <td>{{ number_format($row->views) }}</td>
                    <td>{{ number_format($row->product_clicks) }}</td>
                    <td>{{ number_format($row->items_sold) }}</td>
                    <td>{{ round($row->entry_rate, 2) . '%'}}</td>
                    <td>{{ round($row->ctr, 2) . '%' }}</td>
                    <td>{{ round($row->ctor, 2) . '%'}}</td>
                </tr>
                @endforeach
                <tr class="fw-bold bg-light">
   <tr class="fw-bold bg-light">
    <td>Tổng</td>
    <td>{{ number_format($differences->sum('gmv')) }}</td>
    <td>{{ number_format($differences->count() * $targetPerHour) }}</td>
    <td>
        @php
            $percentAchieved = $targetPerHour > 0 ? round($differences->sum('gmv') / ($differences->count() * $targetPerHour) * 100, 2) : null;
        @endphp
        {{ $percentAchieved !== null ? $percentAchieved . '%' : '-' }}
    </td>
    <td>{{ number_format($differences->sum('ads_total_cost')) }}</td>
    <td>{{ number_format($differences->sum('ads_manual_cost')) }}</td>
    <td>{{ number_format($differences->sum('ads_auto_cost')) }}</td>
    <td>{{ number_format($differences->sum('live_impressions')) }}</td>
    <td>{{ number_format($differences->sum('views')) }}</td>
    <td>{{ number_format($differences->sum('product_clicks')) }}</td>
    <td>{{ number_format($differences->sum('items_sold')) }}</td>
    <td>
      ---
    </td>
    <td>
       --
    </td>
    <td>
        -
    </td>
</tr>

</tr>

            </tbody>
        </table>
    </div>
</div>
@endsection
