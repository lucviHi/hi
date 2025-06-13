@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Chỉnh sửa Nhân Viên</h2>
    
    <form action="{{ route('staffs.update', $staff->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="staff_code" class="form-label">Mã Nhân Viên</label>
            <input type="text" class="form-control" id="staff_code" name="staff_code" value="{{ $staff->staff_code }}" required>
        </div>

        <div class="mb-3">
            <label for="name" class="form-label">Họ và Tên</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $staff->name }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ $staff->email }}">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Mật khẩu mới (nếu đổi)</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Để trống nếu không đổi">
        </div>
        
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Nhập lại mật khẩu mới">
        </div>
        
        <button type="submit" class="btn btn-success">Cập nhật</button>
        <a href="{{ route('staffs.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection
