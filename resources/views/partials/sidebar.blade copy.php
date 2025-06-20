@php
    $isAdmin = request()->is('admin/*') || Auth::guard('admin')->check();
    $user = $isAdmin ? Auth::guard('admin')->user() : Auth::user();

    $roomList = $isAdmin
        ? ($rooms ?? [])
        : ($user?->rooms ?? []);
@endphp


<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
  <div class="sidebar-brand">
    <a href="/" class="brand-link">
      <span class="brand-text fw-light">Admin LIVE</span>
    </a>
  </div>

  <div class="sidebar-wrapper">
    <nav class="mt-2">
      <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">

        {{-- Dashboard (chỉ admin) --}}
        @if ($isAdmin)
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon bi bi-box-seam-fill"></i>
            <p>Dashboard <i class="nav-arrow bi bi-chevron-right"></i></p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon bi bi-circle"></i><p>Doanh thu</p></a></li>
            <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon bi bi-circle"></i><p>Chi Phí</p></a></li>
            <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon bi bi-circle"></i><p>Hoa Hồng</p></a></li>
          </ul>
        </li>
        @endif

        {{-- Danh sách kênh --}}
        @if ($isAdmin)
          <li class="nav-item">
            <a href="{{ route('rooms.index') }}" class="nav-link">
              <i class="nav-icon bi bi-palette"></i>
              <p>Danh sách Kênh</p>
            </a>
          </li>
        @else
          <li class="nav-item">
            <a href="{{ route('rooms.index') }}" class="nav-link">
              <i class="nav-icon bi bi-palette"></i>
              <p>Kênh của tôi</p>
            </a>
          </li>
        @endif

        {{-- Kênh quản lý / của tôi --}}
        @if (!empty($roomList))
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon bi bi-tv"></i>
            <p>
              Kênh {{ $isAdmin ? 'quản lý' : 'của tôi' }}
              <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            @foreach ($roomList as $room)
              <li class="nav-item">
                <a href="{{ $isAdmin ? route('rooms.show', $room->id) : route('rooms.show', $room->id) }}" class="nav-link">
                  <i class="nav-icon bi bi-circle"></i>
                  <p>{{ $room->name }}</p>
                </a>
              </li>
            @endforeach
          </ul>
        </li>
        @endif

        {{-- Quản trị (admin only) --}}
        @if ($isAdmin)
          <li class="nav-item"><a href="{{ route('projects.index') }}" class="nav-link"><i class="nav-icon bi bi-shop"></i><p>Shop</p></a></li>
          <li class="nav-item"><a href="{{ route('platforms.index') }}" class="nav-link"><i class="nav-icon bi bi-laptop"></i><p>Dự án</p></a></li>
          <li class="nav-item"><a href="{{ route('roles.index') }}" class="nav-link"><i class="nav-icon bi bi-person-badge"></i><p>Vai trò</p></a></li>
          <li class="nav-item"><a href="{{ route('staffs.index') }}" class="nav-link"><i class="nav-icon bi bi-people-fill"></i><p>Nhân viên</p></a></li>
        @endif

      </ul>
    </nav>
  </div>
</aside>
