@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Chỉnh Sửa Ngày Live: {{ $liveDay->live_date }}</h2>

    <form action="{{ route('live_days.update', $liveDay->live_date) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">GMV Mục Tiêu</label>
            <input type="number" step="0.01" name="gmv_target" class="form-control" value="{{ $liveDay->gmv_target }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Loại Ngày</label>
            <select name="day_type" class="form-control">
                <option value="normal" {{ $liveDay->day_type == 'normal' ? 'selected' : '' }}>Normal</option>
                <option value="sale" {{ $liveDay->day_type == 'sale' ? 'selected' : '' }}>Sale</option>
                <option value="key" {{ $liveDay->day_type == 'key' ? 'selected' : '' }}>Key</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Cập Nhật</button>
        <a href="{{ route('live_days.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection
