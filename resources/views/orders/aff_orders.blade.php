@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-3">Quản lý Đơn Hàng Affiliate</h2>

    <!-- Nhập file -->
    <form action="{{ route('affiliate_orders.import', ['project_id' => $project_id]) }}" method="POST" enctype="multipart/form-data" class="mb-4">
        @csrf
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Chọn file</label>
                <input type="file" name="file" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Ngày tạo:</label>
                <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">Import</button>
            </div>
        </div>
    </form>

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
                    <th>Ngày tạo</th>
                    <th>ID đơn hàng</th>
                    <th>Tên SP</th>
                    <th>SKU</th>
                    <th>ID SKU</th>
                    <th>SKU người bán</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Thanh toán</th>
                    <th>Trạng thái</th>
                    <th>Loại nội dung</th>
                    <th>Tỷ lệ hoa hồng (%)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($order->order_created_at)->format('d/m/Y H:i:s') }}</td>
                    <td>{{ $order->order_id }}</td>
                    <td>{{ $order->product_name }}</td>
                    <td>{{ $order->sku }}</td>
                    <td>{{ $order->sku_id }}</td>
                    <td>{{ $order->seller_sku }}</td>
                    <td>{{ number_format($order->price) }}</td>
                    <td>{{ $order->quantity }}</td>
                    <td>{{ number_format($order->payment_amount) }}</td>
                    <td>{{ $order->status }}</td>
                    <td>{{ $order->content_type }}</td>
                    <td>{{ $order->commission_rate }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
