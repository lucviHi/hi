@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Chỉnh sửa Dự Án</h2>

    <form action="{{ route('projects.update', $project->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-3">
            <label for="name" class="form-label">Tên Mini CEO</label>
            <input type="text" name="name" class="form-control" value="{{ $project->name }}" required>
        </div>

        <div class="mb-3">
            <label for="platform_id" class="form-label">Dự án</label>
            <select name="platform_id" class="form-control" required>
                @foreach ($platforms as $platform)
                    <option value="{{ $platform->id }}" {{ $project->platform_id == $platform->id ? 'selected' : '' }}>
                        {{ $platform->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-success">Lưu</button>
        <a href="{{ route('projects.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection
