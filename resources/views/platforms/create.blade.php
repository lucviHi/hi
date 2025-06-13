@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="w-50 mx-auto shadow-sm p-4 rounded bg-white">
        <h4 class="fw-bold text-center mb-3"> Thêm Dự án mới</h4>
        <form action="{{ route('platforms.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Tên dự án</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Nhập tên dự án..." required>
            </div>
            <div class="d-flex justify-content-between">
                <a href="{{ route('platforms.index') }}" class="btn btn-light">Quay lại</a>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>
@endsection
