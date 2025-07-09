@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Danh sách Shop</h2>
    <a href="{{ route('projects.create') }}" class="btn btn-primary mb-3">Thêm Mini CEO</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên Mini CEO</th>
                <th>Dự án</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($projects as $project)
                <tr>
                    <td>{{ $loop->iteration}}</td>
                    <td>{{ $project->name }}</td>
                    <td>{{ $project->platform->name }}</td>
                    <td>
                        <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                        <form action="{{ route('projects.destroy', $project->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa?');">Xóa</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
