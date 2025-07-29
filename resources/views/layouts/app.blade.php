<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'BeautyAI Shop') }}</title>
        
        <!-- Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
        <!-- AOS Animation -->
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            :root {
                --primary-color: #ff6b9d;
                --secondary-color: #f8f9fa;
                --accent-color: #ffd700;
                --text-dark: #2c3e50;
                --text-light: #6c757d;
                --gradient-primary: linear-gradient(135deg, #ff6b9d 0%, #ff8fab 100%);
                --gradient-secondary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                --shadow-soft: 0 10px 30px rgba(0,0,0,0.1);
                --shadow-hover: 0 20px 40px rgba(0,0,0,0.15);
            }

            * {
                font-family: 'Poppins', sans-serif;
            }

            .navbar-brand {
                font-family: 'Playfair Display', serif;
                font-weight: 700;
                font-size: 1.8rem;
                background: var(--gradient-primary);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            .navbar {
                background: rgba(255, 255, 255, 0.95) !important;
                backdrop-filter: blur(10px);
                box-shadow: var(--shadow-soft);
                transition: all 0.3s ease;
            }

            .navbar-nav .nav-link {
                font-weight: 500;
                color: var(--text-dark) !important;
                transition: all 0.3s ease;
                position: relative;
            }

            .navbar-nav .nav-link:hover {
                color: var(--primary-color) !important;
                transform: translateY(-2px);
            }

            .navbar-nav .nav-link::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 50%;
                width: 0;
                height: 2px;
                background: var(--gradient-primary);
                transition: all 0.3s ease;
                transform: translateX(-50%);
            }

            .navbar-nav .nav-link:hover::after {
                width: 100%;
            }

            .dropdown-menu {
                border: none;
                box-shadow: var(--shadow-hover);
                border-radius: 15px;
                padding: 1rem 0;
            }

            .dropdown-item {
                padding: 0.75rem 1.5rem;
                transition: all 0.3s ease;
            }

            .dropdown-item:hover {
                background: var(--gradient-primary);
                color: white;
                transform: translateX(5px);
            }

            .cart-badge {
                position: absolute;
                top: -8px;
                right: -8px;
                background: var(--gradient-primary);
                color: white;
                border-radius: 50%;
                width: 22px;
                height: 22px;
                font-size: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 600;
                animation: pulse 2s infinite;
            }

            @keyframes pulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.1); }
                100% { transform: scale(1); }
            }

            .btn-primary {
                background: var(--gradient-primary);
                border: none;
                border-radius: 25px;
                padding: 0.75rem 2rem;
                font-weight: 600;
                transition: all 0.3s ease;
                box-shadow: var(--shadow-soft);
            }

            .btn-primary:hover {
                transform: translateY(-3px);
                box-shadow: var(--shadow-hover);
            }

            .card {
                border: none;
                border-radius: 20px;
                box-shadow: var(--shadow-soft);
                transition: all 0.3s ease;
                overflow: hidden;
            }

            .card:hover {
                transform: translateY(-10px);
                box-shadow: var(--shadow-hover);
            }

            .ai-chatbot {
                position: fixed;
                bottom: 30px;
                right: 30px;
                z-index: 1000;
            }

            .ai-chatbot-btn {
                width: 60px;
                height: 60px;
                border-radius: 50%;
                background: var(--gradient-primary);
                border: none;
                color: white;
                font-size: 24px;
                box-shadow: var(--shadow-hover);
                transition: all 0.3s ease;
                animation: float 3s ease-in-out infinite;
            }

            .ai-chatbot-btn:hover {
                transform: scale(1.1);
            }

            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-10px); }
            }

            .ai-chat-window {
                position: fixed;
                bottom: 100px;
                right: 30px;
                width: 350px;
                height: 500px;
                background: white;
                border-radius: 20px;
                box-shadow: var(--shadow-hover);
                display: none;
                z-index: 1000;
                overflow: hidden;
            }

            .ai-chat-header {
                background: var(--gradient-primary);
                color: white;
                padding: 1rem;
                text-align: center;
                font-weight: 600;
            }

            .ai-chat-messages {
                height: 350px;
                overflow-y: auto;
                padding: 1rem;
            }

            .ai-chat-input {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                padding: 1rem;
                border-top: 1px solid #eee;
                background: white;
            }

            .hero-section {
                background: var(--gradient-secondary);
                color: white;
                padding: 4rem 0;
                position: relative;
                overflow: hidden;
            }

            .hero-section::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            }

            .footer {
                background: var(--text-dark);
                color: white;
                padding: 3rem 0 1rem;
            }

            .footer h5 {
                font-family: 'Playfair Display', serif;
                color: var(--primary-color);
            }

            .social-links a {
                color: white;
                font-size: 1.5rem;
                margin-right: 1rem;
                transition: all 0.3s ease;
            }

            .social-links a:hover {
                color: var(--primary-color);
                transform: translateY(-3px);
            }

            .search-bar {
                background: white;
                border-radius: 25px;
                padding: 0.5rem;
                box-shadow: var(--shadow-soft);
                border: 2px solid transparent;
                transition: all 0.3s ease;
            }

            .search-bar:focus-within {
                border-color: var(--primary-color);
                box-shadow: var(--shadow-hover);
            }

            .search-input {
                border: none;
                outline: none;
                padding: 0.5rem 1rem;
                width: 100%;
                border-radius: 20px;
            }

            .category-badge {
                background: var(--gradient-primary);
                color: white;
                padding: 0.5rem 1rem;
                border-radius: 20px;
                font-size: 0.8rem;
                font-weight: 600;
                margin-bottom: 1rem;
                display: inline-block;
            }

            .price-tag {
                background: var(--gradient-secondary);
                color: white;
                padding: 0.5rem 1rem;
                border-radius: 15px;
                font-weight: 700;
                font-size: 1.1rem;
            }

            .rating-stars {
                color: var(--accent-color);
            }

            .product-card {
                position: relative;
                overflow: hidden;
            }

            .product-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
                transition: left 0.5s;
            }

            .product-card:hover::before {
                left: 100%;
            }

            .ai-recommendation {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 1rem;
                border-radius: 15px;
                margin-bottom: 1rem;
                position: relative;
                overflow: hidden;
            }

            .ai-recommendation::before {
                content: '🤖 AI';
                position: absolute;
                top: 0.5rem;
                right: 1rem;
                font-size: 0.8rem;
                opacity: 0.7;
            }
        </style>
    </head>
<body class="font-sans antialiased">
    <div class="min-vh-100 d-flex flex-column">
        <!-- Main Navigation -->
        <nav class="navbar navbar-expand-lg fixed-top">
            <div class="container">
                <!-- Logo/Brand -->
                <a class="navbar-brand" href="/">
                    <i class="fas fa-spa me-2"></i>BeautyAI
                </a>

                <!-- Mobile Toggle -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Navigation Items -->
                <div class="collapse navbar-collapse" id="mainNavbar">
                    <ul class="navbar-nav me-auto">
                        <!-- Public Links -->
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('shop') }}">
                                <i class="fas fa-shopping-bag me-1"></i>Mỹ phẩm
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#categories">
                                <i class="fas fa-tags me-1"></i>Danh mục
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#ai-consultation">
                                <i class="fas fa-robot me-1"></i>Tư vấn AI
                            </a>
                        </li>
                        
                        @auth
                            <!-- User Links -->
                            @if(Auth::user()->role === 'admin')
                                <!-- Admin Links -->
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-cog me-1"></i>Quản trị
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.products.index') }}">
                                            <i class="fas fa-box me-2"></i>Quản lý sản phẩm
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.orders.index') }}">
                                            <i class="fas fa-shopping-cart me-2"></i>Quản lý đơn hàng
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.reviews.index') }}">
                                            <i class="fas fa-star me-2"></i>Quản lý đánh giá
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="{{ route('shop') }}">
                                            <i class="fas fa-store me-2"></i>Xem trang Shop
                                        </a></li>
                                    </ul>
                                </li>
                            @else
                                <!-- Regular User Links -->
                                <li class="nav-item">
                                    <a class="nav-link position-relative" href="{{ route('cart.index') }}">
                                        <i class="fas fa-shopping-cart me-1"></i>Giỏ hàng
                                        @php
                                            $cartCount = 0;
                                            if (auth()->check()) {
                                                $cartCount = \App\Models\Cart::where('user_id', auth()->id())->sum('quantity');
                                            } else {
                                                $cart = session()->get('cart', []);
                                                $cartCount = array_sum(array_column($cart, 'quantity'));
                                            }
                                        @endphp
                                        @if($cartCount > 0)
                                            <span class="cart-badge">{{ $cartCount }}</span>
                                        @endif
                                    </a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-user me-1"></i>Tài khoản
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">
                                            <i class="fas fa-user-edit me-2"></i>Thông tin cá nhân
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('orders.history') }}">
                                            <i class="fas fa-history me-2"></i>Lịch sử đơn hàng
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('reviews.my') }}">
                                            <i class="fas fa-star me-2"></i>Đánh giá của tôi
                                        </a></li>
                                    </ul>
                                </li>
                            @endif
                        @endauth
                    </ul>

                    <!-- Right Side Menu -->
                    <ul class="navbar-nav ms-auto">
                        @guest
                            <!-- Guest Links -->
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">
                                    <i class="fas fa-sign-in-alt me-1"></i>Đăng nhập
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">
                                    <i class="fas fa-user-plus me-1"></i>Đăng ký
                                </a>
                            </li>
                        @else
                            <!-- User Menu -->
                            <li class="nav-item dropdown user-menu">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user-circle me-1"></i>
                                    {{ Auth::user()->name }}
                                    @if(Auth::user()->role === 'admin')
                                        <span class="badge bg-warning text-dark ms-1">Admin</span>
                                    @endif
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        <i class="fas fa-user-edit me-2"></i>Thông tin cá nhân
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="flex-fill" style="margin-top: 80px;">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- AI Chatbot -->
        <div class="ai-chatbot">
            <button class="ai-chatbot-btn" onclick="toggleChat()">
                <i class="fas fa-robot"></i>
            </button>
            <div class="ai-chat-window" id="aiChatWindow">
                <div class="ai-chat-header">
                    <i class="fas fa-robot me-2"></i>BeautyAI Assistant
                </div>
                <div class="ai-chat-messages" id="aiChatMessages">
                    <div class="text-center text-muted mt-3">
                        <i class="fas fa-spa fa-2x mb-2"></i>
                        <p>Xin chào! Tôi là BeautyAI, trợ lý tư vấn mỹ phẩm của bạn.</p>
                        <p>Hãy hỏi tôi về sản phẩm phù hợp với làn da của bạn!</p>
                    </div>
                </div>
                <div class="ai-chat-input">
                    <div class="input-group">
                        <input type="text" class="form-control" id="aiChatInput" placeholder="Nhập câu hỏi...">
                        <button class="btn btn-primary" onclick="sendMessage()">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <h5><i class="fas fa-spa me-2"></i>BeautyAI Shop</h5>
                        <p>Chuỗi cửa hàng mỹ phẩm cao cấp với công nghệ AI tư vấn chuyên nghiệp. Cam kết mang đến những sản phẩm chất lượng và trải nghiệm mua sắm tuyệt vời.</p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-facebook"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-tiktok"></i></a>
                            <a href="#"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                    <div class="col-md-2 mb-4">
                        <h6>Danh mục</h6>
                        <ul class="list-unstyled">
                            <li><a href="#" class="text-light">Chăm sóc da</a></li>
                            <li><a href="#" class="text-light">Trang điểm</a></li>
                            <li><a href="#" class="text-light">Nước hoa</a></li>
                            <li><a href="#" class="text-light">Chăm sóc tóc</a></li>
                        </ul>
                    </div>
                    <div class="col-md-2 mb-4">
                        <h6>Hỗ trợ</h6>
                        <ul class="list-unstyled">
                            <li><a href="#" class="text-light">Tư vấn AI</a></li>
                            <li><a href="#" class="text-light">Hướng dẫn mua</a></li>
                            <li><a href="#" class="text-light">Chính sách đổi trả</a></li>
                            <li><a href="#" class="text-light">Liên hệ</a></li>
                        </ul>
                    </div>
                    <div class="col-md-4 mb-4">
                        <h6>Đăng ký nhận tin</h6>
                        <p>Nhận thông tin về sản phẩm mới và khuyến mãi đặc biệt</p>
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="Email của bạn">
                            <button class="btn btn-primary">Đăng ký</button>
                        </div>
                    </div>
                </div>
                <hr class="my-4">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-0">&copy; 2024 BeautyAI Shop. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p class="mb-0">Được phát triển với <i class="fas fa-heart text-danger"></i> và AI</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        // Initialize AOS
        AOS.init();

        // AI Chatbot functionality
        function toggleChat() {
            const chatWindow = document.getElementById('aiChatWindow');
            chatWindow.style.display = chatWindow.style.display === 'block' ? 'none' : 'block';
        }

        function sendMessage() {
            const input = document.getElementById('aiChatInput');
            const message = input.value.trim();
            if (message) {
                addMessage('user', message);
                input.value = '';
                
                // Simulate AI response
                setTimeout(() => {
                    const aiResponse = generateAIResponse(message);
                    addMessage('ai', aiResponse);
                }, 1000);
            }
        }

        function addMessage(type, message) {
            const messagesContainer = document.getElementById('aiChatMessages');
            const messageDiv = document.createElement('div');
            messageDiv.className = `mb-3 ${type === 'user' ? 'text-end' : ''}`;
            
            const messageBubble = document.createElement('div');
            messageBubble.className = `d-inline-block p-3 rounded-3 ${
                type === 'user' 
                    ? 'bg-primary text-white' 
                    : 'bg-light text-dark'
            }`;
            messageBubble.style.maxWidth = '80%';
            messageBubble.textContent = message;
            
            messageDiv.appendChild(messageBubble);
            messagesContainer.appendChild(messageDiv);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        function generateAIResponse(message) {
            const responses = {
                'da khô': 'Với làn da khô, tôi khuyên bạn nên sử dụng kem dưỡng ẩm có chứa Hyaluronic Acid và Ceramides. Sản phẩm phù hợp: Kem dưỡng ẩm chuyên sâu.',
                'da dầu': 'Làn da dầu cần sản phẩm kiểm soát bã nhờn. Tôi gợi ý: Sữa rửa mặt gel và kem dưỡng ẩm không gây nhờn.',
                'da nhạy cảm': 'Da nhạy cảm cần sản phẩm dịu nhẹ. Hãy thử: Sữa rửa mặt dành cho da nhạy cảm và kem dưỡng ẩm phục hồi.',
                'mụn': 'Để trị mụn hiệu quả, tôi khuyên: Sản phẩm chứa Salicylic Acid hoặc Benzoyl Peroxide.',
                'chống lão hóa': 'Sản phẩm chống lão hóa tốt nhất: Serum Vitamin C, Retinol và kem chống nắng SPF 50+.',
                'trang điểm': 'Để trang điểm đẹp tự nhiên: Kem nền phù hợp với tone da, phấn phủ và son môi.',
                'tẩy trang': 'Tẩy trang hiệu quả: Dầu tẩy trang hoặc nước tẩy trang dịu nhẹ.'
            };

            const lowerMessage = message.toLowerCase();
            for (const [key, response] of Object.entries(responses)) {
                if (lowerMessage.includes(key)) {
                    return response;
                }
            }

            return 'Cảm ơn bạn đã hỏi! Tôi có thể tư vấn về: da khô, da dầu, da nhạy cảm, trị mụn, chống lão hóa, trang điểm, tẩy trang. Bạn quan tâm đến vấn đề gì?';
        }

        // Enter key to send message
        document.getElementById('aiChatInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.background = 'rgba(255, 255, 255, 0.98)';
                navbar.style.boxShadow = '0 5px 20px rgba(0,0,0,0.1)';
            } else {
                navbar.style.background = 'rgba(255, 255, 255, 0.95)';
                navbar.style.boxShadow = '0 10px 30px rgba(0,0,0,0.1)';
            }
        });
    </script>
</body>
</html>
