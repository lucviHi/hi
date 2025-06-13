@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Danh sách bản ghi đã xóa - Room: {{ $room->name }}</h2>

    <div class="mb-3 d-flex justify-content-between">
        <a href="{{ route('ads_gmv_max_data_days.index', $room->id) }}" class="btn btn-primary">Quay lại danh sách</a>
        <form action="{{ route('ads_gmv_max_data_days.restore_all', $room->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success">Khôi phục tất cả</button>
        </form>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Ngày</th>
                <th>Tên phiên live</th>
                <th>Trạng thái</th>
                <th>Thời gian bắt đầu</th>
                <th>Thời lượng (phút)</th>
                <th>Chi phí</th>
                <th>Chi phí ròng</th>
                <th>Đơn hàng SKU</th>
                <th>Chi phí/đơn hàng</th>
                <th>Doanh thu gộp</th>
                <th>ROI</th>
                <th>Lượt xem live</th>
                <th>Chi phí/lượt xem</th>
                <th>Lượt xem 10s</th>
                <th>Chi phí/lượt xem 10s</th>
                <th>Người theo dõi</th>
                <th>Đơn hàng cửa hàng</th>
                <th>Chi phí/đơn hàng cửa hàng</th>
                <th>Doanh thu gộp cửa hàng</th>
                <th>ROI cửa hàng</th>
                <th>Loại tiền</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($trashedData as $item)
            <tr>
                <td>{{ $item->date }}</td>
                <td>{{ $item->live_session_name }}</td>
                <td>{{ $item->status }}</td>
                <td>{{ $item->launch_time }}</td>
                <td>{{ $item->duration }}</td>
                <td>{{ $item->cost }}</td>
                <td>{{ $item->net_cost }}</td>
                <td>{{ $item->sku_orders }}</td>
                <td>{{ $item->cost_per_order }}</td>
                <td>{{ $item->gross_revenue }}</td>
                <td>{{ $item->roi }}</td>
                <td>{{ $item->live_views }}</td>
                <td>{{ $item->cost_per_view }}</td>
                <td>{{ $item->ten_sec_views }}</td>
                <td>{{ $item->cost_per_ten_sec_view }}</td>
                <td>{{ $item->followers }}</td>
                <td>{{ $item->store_orders }}</td>
                <td>{{ $item->cost_per_store_order }}</td>
                <td>{{ $item->gross_revenue_store }}</td>
                <td>{{ $item->roi_store }}</td>
                <td>{{ $item->currency }}</td>
                <td>
                    <form action="{{ route('ads_gmv_max_data_days.restore', $item->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">Khôi phục</button>
                    </form>
                    <form action="{{ route('ads_gmv_max_data_days.force_delete', $item->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn?')">Xóa vĩnh viễn</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $data->links('vendor.pagination.bootstrap-4') }}
</div>
@endsection
