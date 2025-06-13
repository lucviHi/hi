@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Chỉnh sửa nhân viên: {{ $staffRole->staff->name }}</h2>

    <form action="{{ route('staff_roles.update', ['room' => $room, 'staffRole' => $staffRole]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Chức vụ</label>
            <select name="role_id" class="form-control">
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ $staffRole->role_id == $role->id ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="{{ route('staff_roles.index', ['room' => $room]) }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection
