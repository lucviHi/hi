@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Danh sách Ads Manual Data Days đã bị xóa</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <!-- Các tiêu đề cột -->
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
            @foreach($trashedData as $item)
                <tr>
                    <!-- Các cột dữ liệu -->
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
                        <form action="{{ route('ads_manual_data_days.restore', $item->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">Khôi phục</button>
                        </form>
                        <form action="{{ route('ads_manual_data_days.forceDelete', $item->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn bản ghi này không?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Xóa vĩnh viễn</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $trashedData->links('vendor.pagination.bootstrap-4')}}
</div>
@endsection