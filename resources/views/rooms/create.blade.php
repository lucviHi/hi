@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Thêm Phòng</h2>

    <form action="{{ route('rooms.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Tên Phòng</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="project_id" class="form-label">Dự Án</label>
            <select name="project_id" class="form-control" required>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Thêm</button>
        <a href="{{ route('rooms.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection
