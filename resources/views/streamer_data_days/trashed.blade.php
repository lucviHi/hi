@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Danh sách Streamer Data Days đã bị xóa</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Ngày</th>
                <th>Tên phiên live</th>
                <th>Thời gian bắt đầu</th>
                <th>Thời lượng</th>
                <th>Doanh thu</th>
                <th>GMV</th>
                <th>Đơn hàng</th>
                <th>Lượt xem</th>
                <th>Người xem</th>
                <th>Lượt theo dõi mới</th>
                <th>CTR</th>
                <th>CTOR</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($trashedData as $item)
                <tr>
                    <td>{{ $item->date }}</td>
                    <td>{{ $item->live_name }}</td>
                    <td>{{ $item->start_time }}</td>
                    <td>{{ $item->duration }}</td>
                    <td>{{ $item->total_revenue }}</td>
                    <td>{{ $item->gmv }}</td>
                    <td>{{ $item->items_sold }}</td>
                    <td>{{ $item->views }}</td>
                    <td>{{ $item->viewers }}</td>
                    <td>{{ $item->new_followers }}</td>
                    <td>{{ $item->ctr }}</td>
                    <td>{{ $item->ctor }}</td>
                    <td>
                        <form action="{{ route('streamer_data_days.restore', $item->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">Khôi phục</button>
                        </form>
                        <form action="{{ route('streamer_data_days.forceDelete', $item->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn bản ghi này không?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Xóa vĩnh viễn</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $trashedData->links('vendor.pagination.bootstrap-4') }}
</div>
@endsection
