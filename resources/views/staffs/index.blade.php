@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Danh sách Nhân Viên</h2>

    <!-- Nút Thêm Nhân Viên -->
    <a href="{{ route('staffs.create') }}" class="btn btn-primary mb-3">Thêm Nhân Viên</a>

    <!-- Form Tìm Kiếm -->
    <form action="{{ route('staffs.index') }}" method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên hoặc mã nhân viên" value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary">Tìm kiếm</button>
        </div>
    </form>

    <!-- Bảng Nhân Viên -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Mã Nhân Viên</th>
                <th>Tên Nhân Viên</th>
                <th>Email</th>
                <th>Mật khẩu</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($staffs as $staff)
                <tr>
                    <td>{{ $staff->id }}</td>
                    <td>{{ $staff->staff_code }}</td>
                    <td>{{ $staff->name }}</td>
                    <td>{{ $staff->email }}</td>
                    
                    {{-- Không hiển thị mật khẩu thật --}}
                    <td>
                        ••••••• 
                        {{-- Nếu cần: <a href="{{ route('staffs.reset_password', $staff->id) }}">Đặt lại</a> --}}
                    </td>
    
                    <td>
                        <a href="{{ route('staffs.edit', $staff->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                        <form action="{{ route('staffs.destroy', $staff->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa?');">Xóa</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    

    <!-- Phân trang -->
    <div class="d-flex justify-content-center">
        {{ $staffs->links() }}
    </div>
</div>
@endsection
