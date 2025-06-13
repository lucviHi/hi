@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Chỉnh sửa Chức vụ</h2>
    
    <form action="{{ route('roles.update', $role->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-3">
            <label for="name" class="form-label">Tên Chức vụ</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $role->name }}" required>
        </div>
        
        <div class="mb-3">
            <label for="commission_percentage" class="form-label"> Hoa hồng (%)</label>
            <input type="number" class="form-control" id="commission_percentage" name="commission_percentage" min="0" max="100" step="0.1" value="{{ $role->commission_percentage }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection
