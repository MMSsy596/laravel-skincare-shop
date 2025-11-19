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
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="admin-sidebar">
                    <div class="p-3 text-center">
                        <h5 class="text-white">Admin Panel</h5>
                    </div>
                    <nav class="nav flex-column">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
                            <i class="fas fa-box me-2"></i> Quản lý sản phẩm
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
                            <i class="fas fa-shopping-cart me-2"></i> Quản lý đơn hàng
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}" href="{{ route('admin.reviews.index') }}">
                            <i class="fas fa-star me-2"></i> Quản lý đánh giá
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.vouchers.*') ? 'active' : '' }}" href="{{ route('admin.vouchers.index') }}">
                            <i class="fas fa-ticket-alt me-2"></i> Quản lý voucher
                        </a>
                        <a class="nav-link" href="{{ route('shop') }}">
                            <i class="fas fa-store me-2"></i> Xem trang Shop
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
                                    <i class="fas fa-user me-1"></i> Profile
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Page Content -->
                    <div class="p-4">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 