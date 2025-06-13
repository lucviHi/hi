@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Thêm Nhân Viên cho Kênh: {{ $room->name }}</h2>

    <form action="{{ route('staff_roles.store', ['room' => $room->id]) }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Chọn Nhân Viên</label>
            <select name="staff_id" class="form-control select2" style="width: 100%;">
                <option value="">-- Tìm kiếm nhân viên --</option>
                @foreach($staffs as $staff)
                    <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                @endforeach
            </select>             
        </div>

        <div class="mb-3">
            <label class="form-label">Chức vụ</label>
            <select name="role_id" class="form-control">
                @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Thêm Nhân Viên</button>
    </form>
</div>
@endsection

@section('scripts')
<!-- Thêm thư viện jQuery và Select2 -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
    $('.select2').select2({
        placeholder: "Tìm kiếm nhân viên...",
        allowClear: true,
        minimumInputLength: 0, // Bắt đầu tìm kiếm ngay cả khi chưa nhập
        dropdownAutoWidth: true, // Để dropdown không bị nhỏ
        ajax: {
            url: "{{ route('staff.search') }}", // API trả danh sách nhân viên
            dataType: 'json',
            delay: 250,
            processResults: function(data) {
                return {
                    results: data.map(item => ({
                        id: item.id,
                        text: item.name
                    }))
                };
            }
        }
    });
});

</script>
@endsection


