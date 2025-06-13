@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Danh sách Dữ liệu Quảng cáo</h2>
    
    <a href="{{ route('ads_data_days.create', ['room_id' => $room->id]) }}" class="btn btn-primary mb-3">Thêm Mới</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- <h2>Quảng cáo cho kênh: {{ $room->name }}</h2> --}}
    <table>
        <thead>
            <tr>
                <th>Ngày</th>
                <th>Chi phí tự động</th>
                <th>Doanh thu tự động</th>
                <th>Doanh thu thực tự động</th>
                <th>Chi phí thủ công</th>
                <th>ROAS thủ công</th>
                <th>Doanh thu thủ công</th>
                <th>Tổng chi phí</th>
                <th>Tổng doanh thu</th>
                <th>ROAS tổng</th>
            </tr>
        </thead>
        <tbody>
            @foreach($adsDataDay as $data)
            <tr>
                <td>{{ $data->date }}</td>
                <td>{{ $data->gmv_max_cost }}</td>
                <td>{{ $data->gmv_max_gross_revenue }}</td>
                <td>{{ $data->gmv_max_real_revenue }}</td>
                <td>{{ $data->manual_cost }}</td>
                <td>{{ $data->manual_roas }}</td>
                <td>{{ $data->manual_revenue }}</td>
                <td>{{ $data->total_ads_cost }}</td>
                <td>{{ $data->total_ads_revenue }}</td>
                <td>{{ $data->total_roas }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection
