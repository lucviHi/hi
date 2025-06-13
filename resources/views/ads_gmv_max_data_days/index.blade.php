@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Quản lý Dữ liệu Ads GMV Max - Room: {{ $room->name }}</h2>
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-3">
            <a href="{{ route('ads_gmv_max_data_days.import_index', $room->id) }}" class="btn btn-primary">Nhập dữ liệu</a>
            <a href="{{ route('ads_gmv_max_data_days.trashed', $room->id) }}" class="btn btn-warning">Bản ghi đã xóa</a>
        </div>

        <form method="GET" action="{{ route('ads_gmv_max_data_days.index', $room->id) }}" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <label for="start_date">Từ ngày:</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-4">
                    <label for="end_date">Đến ngày:</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-success mt-4">Lọc dữ liệu</button>
                </div>
            </div>
        </form>

        <table class="table table-bordered">
            <thead>
                <tr>
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
                @foreach ($data as $item)
                    <tr>
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
                            <form action="{{ route('ads_gmv_max_data_days.destroy', $item->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="room_id" value="{{ $room->id }}">
                                <button type="submit" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Bạn có chắc chắn muốn xóa?')">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $data->links('vendor.pagination.bootstrap-4') }}
    </div>
@endsection