@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Thêm Chức vụ</h2>
    
    <form action="{{ route('roles.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Tên Chức vụ</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        
        <div class="mb-3">
            <label for="commission_percentage" class="form-label">Hoa hồng (%)</label>
            <input type="number" class="form-control" id="commission_percentage" name="commission_percentage" min="0" max="100" step="0.1" required>
        </div>

        <button type="submit" class="btn btn-primary">Lưu</button>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection
