@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Thêm Ngày Live</h2>

    <form action="{{ route('live_days.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Ngày</label>
            <input type="date" name="live_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">GMV Mục Tiêu</label>
            <input type="number" step="0.01" name="gmv_target" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Loại Ngày</label>
            <select name="day_type" class="form-control">
                <option value="normal">Normal</option>
                <option value="sale">Sale</option>
                <option value="key">Key</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Thêm</button>
    </form>
</div>
@endsection
