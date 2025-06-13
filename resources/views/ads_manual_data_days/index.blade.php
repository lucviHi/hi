@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Danh sách Ads Manual Data Days {{$room->name}}</h2>
    <form method="GET" action="{{ route('ads_manual_data_days.index', ['room_id' => $room->id]) }}" class="mb-4">
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

    <a href="{{ route('ads_manual_data_days.import_index',   $room->id) }}" class="btn btn-primary mb-3">Import dữ liệu</a>
    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Ngày</th>
                <th>Chi phí USD</th>
                <th>Chi phí Local</th>
                <th>CPC USD</th>
                <th>CPA USD</th>
                <th>Tổng lượt mua</th>
                <th>Chi phí mỗi lượt thanh toán</th>
                <th>Hiển thị</th>
                <th>CTR</th>
                <th>CPM</th>
                <th>CPC</th>
                <th>Clicks</th>
                <th>Chuyển đổi</th>
                <th>CVR</th>
                <th>CPA</th>
                <th>ROAS Mua hàng</th>
                <th>ROAS Thanh toán</th>
                <th>ROAS On-site</th>
                <th>Mua sắm</th>
                <th>Số lượng mua</th>
                <th>Chi phí/mua</th>
                <th>Chi phí/mua sắm</th>
                <th>Tổng thanh toán</th>
                <th>Chi phí/thanh toán lặp lại</th>
                <th>Lượt xem video</th>
                <th>Lượt xem video 2s</th>
                <th>Lượt xem video 6s</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
                <tr>
                    <td>{{ $item->date }}</td>
                    <td>{{ $item->cost_usd }}</td>
                    <td>{{ $item->cost_local }}</td>
                    <td>{{ $item->cpc_usd }}</td>
                    <td>{{ $item->cpa_usd }}</td>
                    <td>{{ $item->total_purchases }}</td>
                    <td>{{ $item->cost_per_payment }}</td>
                    <td>{{ $item->impressions }}</td>
                    <td>{{ $item->ctr }}</td>
                    <td>{{ $item->cpm }}</td>
                    <td>{{ $item->cpc }}</td>
                    <td>{{ $item->clicks }}</td>
                    <td>{{ $item->conversions }}</td>
                    <td>{{ $item->cvr }}</td>
                    <td>{{ $item->cpa }}</td>
                    <td>{{ $item->roas_purchase }}</td>
                    <td>{{ $item->roas_payment }}</td>
                    <td>{{ $item->roas_on_site }}</td>
                    <td>{{ $item->shopping_purchases }}</td>
                    <td>{{ $item->purchase_count }}</td>
                    <td>{{ $item->cost_per_purchase }}</td>
                    <td>{{ $item->cost_per_shopping_purchase }}</td>
                    <td>{{ $item->total_payments }}</td>
                    <td>{{ $item->cost_per_payment_repeat }}</td>
                    <td>{{ $item->video_views }}</td>
                    <td>{{ $item->video_views_2s }}</td>
                    <td>{{ $item->video_views_6s }}</td>
                    <td>
                        <form action="{{ route('ads_manual_data_days.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bản ghi này không?');">
                            @csrf
                            @method('DELETE')
                            {{-- <input type="hidden" name="room_id" value="{{ $room->id }}"> --}}
                            <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                        </form>
                    </td>
                    
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $data->links('vendor.pagination.bootstrap-4') }}
    <a href="{{ route('ads_manual_data_days.trashed',   $room->id) }}" style="float: right;" class="btn btn-success mb-3">Khôi phục bản ghi</a>
      
</div>
@endsection
