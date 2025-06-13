@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Phòng Live: {{$room->name}}</h2>
    <form method="GET" action="{{ route('ads_manual_data_days.index', ['room_id' => $room->id]) }}" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <label for="start_date">Ngày bắt đầu:</label>
                <input type="date" id="start_date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="col-md-4">
                <label for="end_date">Ngày kết thúc:</label>
                <input type="date" id="end_date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="col-md-1">
                <label>&nbsp;</label>
                <button type="submit" class="form-control btn btn-primary btn-block">Lọc</button>
            </div>
        </div>
    </form>
    <a href="{{ route('ads_manual_data_days.import_index',   $room->id) }}" class="btn btn-primary mb-3">Import dữ liệu</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Ngày</th>
              
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
                <tr>
                   
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $data->links('vendor.pagination.bootstrap-4') }}
</div>
@endsection
