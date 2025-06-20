@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Danh sách Mục Tiêu Ngày Theo Phòng</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
<form action="{{ route('live_target_days.index', ['room_id' => $room->id]) }}" method="GET" class="mb-3">
    <div class="row g-2 align-items-center">
        <div class="col-md-4">
            <input type="month" name="month" class="form-control" value="{{ $month ?? '' }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-outline-primary w-100">Lọc tháng</button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('live_target_days.index', ['room_id' => $room->id]) }}" class="btn btn-outline-secondary w-100">Xóa lọc</a>
        </div>
    </div>
</form>

    <form action="{{ route('live_target_days.generate', ['room_id' => $room->id]) }}" method="POST">
        @csrf
        <div class="row g-2 mb-3">
            <div class="col-md-6">
                <input type="month" name="month" class="form-control" required>
            </div>
            <div class="col-md-6">
                <button type="submit" class="btn btn-success w-100">Tạo mục tiêu tháng</button>
            </div>
        </div>
    </form>

    <a href="{{ route('live_target_days.create', ['room_id' => $room->id]) }}" class="btn btn-primary mb-3">Thêm Mục Tiêu Ngày</a>

<form action="{{ route('live_target_days.bulk_update', ['room_id' => $room->id]) }}" method="POST">
    @csrf
    @method('PUT')
   <div class="d-flex justify-content-end">
       <button type="submit" class="btn btn-success mb-3">Lưu toàn bộ</button>
   </div>
    <table class="table table-bordered text-center align-middle">
        <thead class="table-light">
            <tr>
                <th>Ngày</th>
                <th>GMV Mục tiêu</th>
                <th>Chi phí tối đa</th>
                <th>Số team</th>
                <th>Loại ngày</th>
                <th>Ghi chú</th>
            </tr>
        </thead>
        <tbody>
            @foreach($targets as $index => $target)
                <tr>
                    <td>
                        {{ $target->date }}
                        <input type="hidden" name="entries[{{ $index }}][date]" value="{{ $target->date }}">
                    </td>
                    <td><input type="text" name="entries[{{ $index }}][gmv_target]" value="{{ number_format($target->gmv_target) }}" class="form-control text-end format-number"></td>
                    <td><input type="text" name="entries[{{ $index }}][cost_limit]" value="{{ number_format($target->cost_limit) }}" class="form-control text-end format-number"></td>
                    <td><input type="text" name="entries[{{ $index }}][team_count]" value="{{ $target->team_count }}" class="form-control text-end"></td>
                    <td>
                        <select name="entries[{{ $index }}][day_type]" class="form-select">
                            <option value="normal" {{ $target->day_type === 'normal' ? 'selected' : '' }}>Thường</option>
                            <option value="sale" {{ $target->day_type === 'sale' ? 'selected' : '' }}>Sale</option>
                            <option value="key" {{ $target->day_type === 'key' ? 'selected' : '' }}>Key</option>
                        </select>
                    </td>
                    <td><input type="text" name="entries[{{ $index }}][note]" value="{{ $target->note }}" class="form-control"></td>
                    
                </tr>
            @endforeach
        </tbody>
    </table>

  
</form>

</div>



@endsection
@section('scripts')
<script>
    document.querySelectorAll('.format-number').forEach(input => {
        input.addEventListener('input', function () {
            let value = this.value.replace(/,/g, '');
            if (!isNaN(value) && value !== '') {
                this.value = parseInt(value).toLocaleString('en-US');
            }
        });
    });

    document.querySelector('form').addEventListener('submit', function () {
        this.querySelectorAll('.format-number').forEach(input => {
            input.value = input.value.replace(/,/g, '');
        });
    });
</script>
@endsection

