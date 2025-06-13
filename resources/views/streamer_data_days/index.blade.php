@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Danh sách Streamer Data Days - {{ $room->name }}</h2>

    {{-- Form lọc theo ngày --}}
    <form method="GET" action="{{ route('streamer_data_days.index', ['room_id' => $room->id]) }}" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <label for="start_date">Ngày bắt đầu:</label>
                <input type="date" id="start_date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="col-md-4">
                <label for="end_date">Ngày kết thúc:</label>
                <input type="date" id="end_date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="col-md-1">
                <label>&nbsp;</label>
                <button type="submit" class="form-control btn btn-primary btn-block">Lọc</button>
            </div>
        </div>
    </form>

    {{-- Thông báo lỗi / thành công --}}
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Nút import dữ liệu --}}
    <a href="{{ route('streamer_data_days.import_index', $room->id) }}" class="btn btn-primary mb-3">Import dữ liệu</a>

    {{-- Bảng hiển thị dữ liệu --}}
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tên phiên LIVE</th>
                <th>Thời gian bắt đầu</th>
                <th>Thời lượng (giây)</th>
                <th>Tổng doanh thu</th>
                <th>GMV</th>
                <th>Sản phẩm đã bán</th>
                <th>Khách hàng</th>
                <th>Giá trung bình</th>
                <th>Đơn hàng thanh toán</th>
                <th>GMV/1K Impression</th>
                <th>GMV/1K Views</th>
                <th>Lượt xem</th>
                <th>Người xem</th>
                <th>Người xem cao nhất</th>
                <th>Người theo dõi mới</th>
                <th>Thời gian xem trung bình</th>
                <th>Lượt thích</th>
                <th>Bình luận</th>
                <th>Chia sẻ</th>
                <th>Hiển thị sản phẩm</th>
                <th>Click sản phẩm</th>
                <th>CTR</th>
                <th>CTOR</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
                <tr>
                    <td>{{ $item->live_name }}</td>
                    <td>{{ $item->start_time }}</td>
                    <td>{{ $item->duration }}</td>
                    <td>{{ number_format($item->total_revenue) }}</td>
                    <td>{{ number_format($item->gmv) }}</td>
                    <td>{{ $item->items_sold }}</td>
                    <td>{{ $item->customers }}</td>
                    <td>{{ number_format($item->avg_price) }}</td>
                    <td>{{ $item->paid_orders }}</td>
                    <td>{{ number_format($item->gmv_per_1k_impressions) }}</td>
                    <td>{{ number_format($item->gmv_per_1k_views) }}</td>
                    <td>{{ $item->views }}</td>
                    <td>{{ $item->viewers }}</td>
                    <td>{{ $item->max_viewers }}</td>
                    <td>{{ $item->new_followers }}</td>
                    <td>{{ $item->avg_watch_time }}</td>
                    <td>{{ $item->likes }}</td>
                    <td>{{ $item->comments }}</td>
                    <td>{{ $item->shares }}</td>
                    <td>{{ $item->product_displays }}</td>
                    <td>{{ $item->product_clicks }}</td>
                    <td>{{ number_format($item->ctr, 4) }}</td>
                    <td>{{ number_format($item->ctor, 4) }}</td>
                    <td>
                        <form action="{{ route('streamer_data_days.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bản ghi này không?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Phân trang --}}
    {{ $data->links('vendor.pagination.bootstrap-4') }}

    {{-- Khôi phục bản ghi đã xóa --}}
    <a href="{{ route('streamer_data_days.trashed', $room->id) }}" style="float: right;" class="btn btn-success mb-3">Khôi phục bản ghi</a>
</div>
@endsection
