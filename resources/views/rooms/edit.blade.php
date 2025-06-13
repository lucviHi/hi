@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Chỉnh sửa Phòng</h2>

    <form action="{{ route('rooms.update', $room->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-3">
            <label for="name" class="form-label">Tên Phòng</label>
            <input type="text" name="name" class="form-control" value="{{ $room->name }}" required>
        </div>

        <div class="mb-3">
            <label for="project_id" class="form-label">Dự Án</label>
            <select name="project_id" class="form-control" required>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}" {{ $room->project_id == $project->id ? 'selected' : '' }}>
                        {{ $project->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-success">Lưu</button>
        <a href="{{ route('rooms.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection
