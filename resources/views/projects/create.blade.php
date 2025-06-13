@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Thêm Shop</h2>

    <form action="{{ route('projects.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Tên Shop</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="platform_id" class="form-label">Dự án</label>
            <select name="platform_id" class="form-control" required>
                @foreach ($platforms as $platform)
                    <option value="{{ $platform->id }}">{{ $platform->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Thêm</button>
        <a href="{{ route('projects.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection
