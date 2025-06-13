@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Danh sách Nhân Viên Kênh {{$room->name}}</h2>

    <a href="{{ route('staff_roles.create', ['room' => $room->id]) }}" class="btn btn-primary mb-3">
        Thêm Nhân Viên
    </a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nhân viên</th>
                <th>Chức vụ</th>
                <th>Kênh</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($staffRoles as $staffRole)
                <tr>
                    <td>{{ $staffRole->id }}</td>
                    <td>{{ $staffRole->staff->name }}</td>
                    <td>{{ $staffRole->role->name }}</td>
                    <td>{{ $staffRole->room->name }}</td>
                    <td>
                        <a href="{{ route('staff_roles.edit', ['room' => $room->id, 'staffRole' => $staffRole->id]) }}" 
                           class="btn btn-warning btn-sm">Sửa</a>
                    
                        <form action="{{ route('staff_roles.destroy', ['room' => $room->id, 'staffRole' => $staffRole->id]) }}" 
                              method="POST" 
                              style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" 
                                    onclick="return confirm('Bạn có chắc chắn muốn xóa?')">Xóa</button>
                        </form>
                    </td>
                    
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $staffRoles->links() }}
    </div>
</div>
@endsection
