@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-4">📊 Snapshot Daily: Tổng hợp từ <strong> {{ $from }} </strong> đến <strong> {{ $to }} </strong></h3>

    <form method="GET" class="row g-3 align-items-end mb-4">
        <div class="col-md-2">
            <label class="form-label">Từ ngày</label>
            <input type="date" name="from_date" value="{{ $from }}" class="form-control">
        </div>

        <div class="col-md-2">
            <label class="form-label">Đến ngày</label>
            <input type="date" name="to_date" value="{{ $to }}" class="form-control">
        </div>

        <div class="col-md-3">
            <label class="form-label">Dự án</label>
            <select name="project_id" class="form-select">
                <option value="">-- Tất cả --</option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}" {{ $selectedProject == $project->id ? 'selected' : '' }}>
                        {{ $project->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Phòng</label>
            <select name="room_id" class="form-select">
                <option value="">-- Tất cả --</option>
                @foreach ($rooms as $room)
                    <option value="{{ $room->id }}" {{ $selectedRoom == $room->id ? 'selected' : '' }}>
                        {{ $room->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">🔍 Xem</button>
        </div>
    </form>

    @php
        $totalGmv = $snapshot->sum('gmv');
        $totalTarget = $snapshot->sum('gmv_target');
        $totalAds = $snapshot->sum('ads_total_cost');
        $totalDeal = $snapshot->sum('deal_cost');
        $totalCost = $snapshot->sum('total_cost');
        $totalImpressions = $snapshot->sum('live_impressions');
        $totalViews = $snapshot->sum('views');
        $totalClicks = $snapshot->sum('product_clicks');
        $totalItems = $snapshot->sum('items_sold');

        $totalEntryRate = $totalImpressions > 0 ? round($totalViews / $totalImpressions * 100, 2) : null;
        $totalCtr = $totalViews > 0 ? round($totalClicks / $totalViews * 100, 2) : null;
        $totalCtor = $totalClicks > 0 ? round($totalItems / $totalClicks * 100, 2) : null;
        $totalPercentAchieved = $totalTarget > 0 ? round($totalGmv / $totalTarget * 100, 2) : null;

    @endphp

    <div class="table-responsive">
        <table class="table table-bordered table-hover text-center align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Phòng</th>
                    <th>GMV</th>
                    <th>Mục tiêu</th>
                    <th>% Đạt</th>
                    <th>Ads</th>
                    <th>Deal</th>
                    <th>Tổng chi phí</th>
                    <th>% Tổng chi phí/ GMV</th>
                    <th>Live Impressions</th>
                    <th>Views</th>
                    <th>Clicks</th>
                    <th>Items Sold</th>
                    <th>Entry Rate (%)</th>
                    <th>CTR (%)</th>
                    <th>CTOR (%)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($snapshot as $row)
                    <tr>
                        <td class="text-start">{{ $row->room->name }}</td>
                        <td>{{ number_format($row->gmv) }}</td>
                        <td>{{ number_format($row->gmv_target) }}</td>
                        <td>{{ $row->percent_achieved !== null ? $row->percent_achieved . '%' : '-' }}</td>
                        <td>{{ number_format($row->ads_total_cost) }}</td>
                        <td>{{ number_format($row->deal_cost) }}</td>
                        <td>{{ number_format($row->total_cost) }}</td>
                        <td>{{ $row->gmv > 0 ? round($row->total_cost *100/ $row->gmv, 2).'%' : '-' }}</td>
                        <td>{{ number_format($row->live_impressions) }}</td>
                        <td>{{ number_format($row->views) }}</td>
                        <td>{{ number_format($row->product_clicks) }}</td>
                        <td>{{ number_format($row->items_sold) }}</td>
                        <td>{{ $row->entry_rate ?? '-' }}</td>
                        <td>{{ $row->ctr ?? '-' }}</td>
                        <td>{{ $row->ctor ?? '-' }}</td>
                    </tr>
                @endforeach

                {{-- Dòng tổng cộng --}}
                <tr class="table-secondary fw-bold">
                    <td>TỔNG</td>
                    <td>{{ number_format($totalGmv) }}</td>
                    <td>{{ number_format($totalTarget) }}</td>
                    <td>{{ $totalPercentAchieved !== null ? $totalPercentAchieved . '%' : '-' }}</td>
                    <td>{{ number_format($totalAds) }}</td>
                    <td>{{ number_format($totalDeal) }}</td>
                    <td>{{ number_format($totalCost) }}</td>
                    <td>{{ $totalGmv > 0 ? round($totalCost*100 / $totalGmv, 2).'%' : '-' }}</td>
                    <td>{{ number_format($totalImpressions) }}</td>
                    <td>{{ number_format($totalViews) }}</td>
                    <td>{{ number_format($totalClicks) }}</td>
                    <td>{{ number_format($totalItems) }}</td>
                    <td>{{ $totalEntryRate ?? '-' }}</td>
                    <td>{{ $totalCtr ?? '-' }}</td>
                    <td>{{ $totalCtor ?? '-' }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection