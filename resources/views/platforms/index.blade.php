@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Danh sách Dự án</h2>
    <a href="{{ route('platforms.create') }}" class="btn btn-primary mb-3">Thêm Dự án</a>

    <div class="table-responsive">
        <table class="table table-hover" style="border-collapse: separate; border-spacing: 0 8px;">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Tên Dự án</th>
                    <th class="text-center">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($platforms as $platform)
                    <tr style="border: 1px solid #dee2e6; border-radius: 5px;">
                        <td class="align-middle">{{ $loop->iteration}}</td>
                        <td class="align-middle">{{ $platform->name }}</td>
                        <td class="text-center align-middle">
                            <a href="{{ route('platforms.edit', $platform->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                            <form action="{{ route('platforms.destroy', $platform->id) }}" method="POST" class="d-inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa?');">
                                    Xóa
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
