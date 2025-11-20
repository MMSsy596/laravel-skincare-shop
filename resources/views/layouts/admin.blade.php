<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - Admin</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" />
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-body">
    <div id="dynamicIsland" class="dynamic-island collapsed info">
        <div class="di-icon"><i class="fas fa-spa"></i></div>
        <div class="di-content"><span class="di-title"></span><span class="di-message"></span></div>
    </div>
    <div id="diSettingsPanel" class="di-settings-panel">
            <div class="di-settings-header">
                <span class="di-settings-title">Cài đặt</span>
                <button class="btn btn-sm btn-outline-primary" onclick="toggleDiSettings(false)"><i class="fas fa-times"></i></button>
            </div>
            <div class="mb-3">
                <label class="form-label">Giao diện</label>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary btn-pill" onclick="setTheme('light')"><i class="fas fa-sun me-1"></i>Sáng</button>
                    <button class="btn btn-outline-primary btn-pill" onclick="setTheme('dark')"><i class="fas fa-moon me-1"></i>Tối</button>
                </div>
            </div>
            <div class="mb-2">
                <label class="form-label">Ngôn ngữ</label>
                <select class="form-select" id="diLanguageSelect" onchange="setLanguage(this.value)">
                    <option value="vi">Tiếng Việt</option>
                    <option value="en">English</option>
                    <option value="zh">中文</option>
                    <option value="ko">한국어</option>
                    <option value="ja">日本語</option>
                </select>
            </div>
    </div>
    <div class="container-fluid">
        <div id="dynamicIsland" class="dynamic-island collapsed info">
            <div class="di-icon"><i class="fas fa-spa"></i></div>
            <div class="di-content"><span class="di-title"></span><span class="di-message"></span></div>
        </div>
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="admin-sidebar">
                    <div class="p-3 text-center">
                        <h5 class="text-white">Admin Panel</h5>
                    </div>
                    <nav class="nav flex-column">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i> <span data-i18n="admin.dashboard">Dashboard</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
                            <i class="fas fa-box me-2"></i> <span data-i18n="admin.products">Quản lý sản phẩm</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
                            <i class="fas fa-shopping-cart me-2"></i> <span data-i18n="admin.orders">Quản lý đơn hàng</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}" href="{{ route('admin.reviews.index') }}">
                            <i class="fas fa-star me-2"></i> <span data-i18n="admin.reviews">Quản lý đánh giá</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.vouchers.*') ? 'active' : '' }}" href="{{ route('admin.vouchers.index') }}">
                            <i class="fas fa-ticket-alt me-2"></i> <span data-i18n="admin.vouchers">Quản lý voucher</span>
                        </a>
                        <a class="nav-link" href="{{ route('shop') }}">
                            <i class="fas fa-store me-2"></i> <span data-i18n="nav.shop_view">Xem trang Shop</span>
                        </a>
                        <hr class="text-white">
                        <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </nav>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 px-0">
                <div class="admin-main">
                    <!-- Top Navbar -->
                    <div class="admin-topbar p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">@yield('page-title', 'Dashboard')</h4>
                            <div class="d-flex align-items-center">
                                <span class="me-3">Xin chào, {{ Auth::user()->name }}</span>
                                <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-user me-1"></i> <span data-i18n="profile">Thông tin cá nhân</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Page Content -->
                    <div class="p-4">
                        
                        
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        window.__flashes = {!! json_encode([
            session('success') ? ['type'=>'success','title'=>'Thành công','message'=>session('success')] : null,
            session('error') ? ['type'=>'error','title'=>'Lỗi','message'=>session('error')] : null,
            session('status') ? ['type'=>'info','title'=>'Thông báo','message'=>session('status')] : null,
        ]) !!}.filter(Boolean);
    </script>
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>