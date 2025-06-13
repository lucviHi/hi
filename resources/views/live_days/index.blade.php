@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Danh Sách Ngày Live</h2>

    <form action="{{ route('live_days.generate') }}" method="POST" class="mb-3">
        @csrf
        <div class="input-group">
            <input type="month" name="month" class="form-control" required>
            <button type="submit" class="btn btn-success">Tạo ngày cho tháng</button>
        </div>
    </form>

    <a href="{{ route('live_days.create') }}" class="btn btn-primary mb-3">Thêm Ngày Live</a>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Ngày</th>
                <th>GMV Mục Tiêu</th>
                <th>Loại Ngày</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($liveDays as $day)
            <tr>
                <td>{{ $day->live_date }}</td>
                <td>{{ number_format($day->gmv_target, 2) }}</td>
                <td>{{ ucfirst($day->day_type) }}</td>
                <td>
                    <a href="{{ route('live_days.edit', $day->live_date) }}" class="btn btn-warning">Sửa</a>
                    <form action="{{ route('live_days.destroy', $day->live_date) }}" method="POST" style="display:inline-block;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
