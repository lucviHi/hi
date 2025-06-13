@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Quản lý Livestream</h2>
        <div class="d-flex align-items-center gap-3" style="position: sticky; top: 0; z-index: 1000;">
            <label for="livestream_date" class="me-2 fw-bold text-nowrap">Chọn ngày:</label>
            <input type="date" name="date" class="form-control" required value="{{ \Carbon\Carbon::today()->toDateString() }}">
            <div class="d-flex gap-2">
                <form action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Chọn file Excel</label>
                        <input type="file" name="file" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Import</button>
                </form>
                <form action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Chọn file Excel</label>
                        <input type="file" name="file" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Import</button>
                </form>
                <form action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Chọn file Excel</label>
                        <input type="file" name="file" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Import</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Chọn</th>
                    <th>Khung Giờ</th>
                    <th>GMV</th>
                    <th>Chi Phí Quảng Cáo</th>
                    <th>ROAS</th>
                    <th>Đơn Hàng</th>
                    <th>Hiển Thị</th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 0; $i < 24; $i++)
                <tr>
                    <td><input type="radio" name="selected_hour" value="{{ $i }}" class="form-check-input"></td>
                    <td>{{ $i }}:00 - {{ $i+1 }}:00</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
                @endfor
            </tbody>
        </table>
    </div>
</div>
@endsection
