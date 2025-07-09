@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Danh sách Phòng</h2>
    <a href="{{ route('rooms.create') }}" class="btn btn-primary mb-3">Thêm Kênh</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên Phòng</th>
                <th>Thuộc Mini CEO</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rooms as $room)
                <tr>
                    <td>{{ $room->id }}</td>
                    <td>{{ $room->name }}</td>
                    <td>{{ $room->project->name }}</td>
                    <td>
                        <a href="{{ route('staff_roles.index', $room->id) }}" class="btn btn-info btn-sm">Xem</a>
                        <a href="{{ route('rooms.edit', $room->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                        <form action="{{ route('rooms.destroy', $room->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa?');">Xóa</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- PHÂN TRANG -->
    {{-- <div class="d-flex justify-content-center mt-3">
        {{ $rooms->appends(request()->query())->links() }}
    </div> --}}
</div>
@endsection
