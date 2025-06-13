@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="w-50 mx-auto shadow-sm p-4 rounded bg-white">
        <h4 class="fw-bold text-center mb-3"> Chỉnh sửa Dự án</h4>
        <form action="{{ route('platforms.update', $platform->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Tên dự án</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $platform->name }}" required>
            </div>
            <div class="d-flex justify-content-between">
                <a href="{{ route('platforms.index') }}" class="btn btn-light">Quay lại</a>
                <button type="submit" class="btn btn-warning">Cập nhật</button>
            </div>
        </form>
    </div>
</div>
@endsection
