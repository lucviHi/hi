@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Thanh điều hướng tab -->
    <ul class="nav nav-tabs" id="channelTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="dashboard-tab" data-toggle="tab" href="#dashboard" role="tab">Dashboard</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="staff-tab" data-toggle="tab" href="#staff" role="tab">Quản lý Nhân viên</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="commission-tab" data-toggle="tab" href="#commission" role="tab">Quản lý Hoa hồng</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="data-tab" data-toggle="tab" href="#data" role="tab">Dữ liệu</a>
        </li>
    </ul>

    <!-- Nội dung các tab -->
    <div class="tab-content" id="channelTabsContent">
        <div class="tab-pane fade show active" id="dashboard" role="tabpanel">
          
        </div>
        <div class="tab-pane fade" id="staff" role="tabpanel">
          
        </div>
        <div class="tab-pane fade" id="commission" role="tabpanel">
          
        </div>
        <div class="tab-pane fade" id="data" role="tabpanel">
          
        </div>
    </div>
</div>
@endsection
