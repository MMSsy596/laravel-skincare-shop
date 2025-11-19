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
    </head>
<body class="font-sans antialiased">
    <div class="min-vh-100 d-flex flex-column">
        <!-- Main Navigation -->
        <nav class="navbar navbar-light navbar-expand-lg fixed-top theme-navbar">
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
        <main class="flex-fill">
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
                        <i class="fas fa-spa fa-2x mb-2 text-primary"></i>
                        <p class="fw-semibold">Xin chào! Tôi là BeautyAI, trợ lý tư vấn mỹ phẩm của bạn.</p>
                        <p class="small">Hãy hỏi tôi về sản phẩm phù hợp với làn da của bạn!</p>
                        
                        <!-- Quick Action Buttons -->
                        <div class="quick-actions mt-3 px-2">
                            <button class="btn btn-sm btn-outline-primary mb-2 quick-action-btn" onclick="sendQuickMessage('Tư vấn cho da khô')">
                                <i class="fas fa-tint me-1"></i>Da khô
                            </button>
                            <button class="btn btn-sm btn-outline-primary mb-2 quick-action-btn" onclick="sendQuickMessage('Tư vấn cho da dầu')">
                                <i class="fas fa-oil-can me-1"></i>Da dầu
                            </button>
                            <button class="btn btn-sm btn-outline-primary mb-2 quick-action-btn" onclick="sendQuickMessage('Tư vấn cho da nhạy cảm')">
                                <i class="fas fa-heart me-1"></i>Da nhạy cảm
                            </button>
                            <button class="btn btn-sm btn-outline-primary mb-2 quick-action-btn" onclick="sendQuickMessage('Sản phẩm nào còn hàng?')">
                                <i class="fas fa-box me-1"></i>Kiểm tra tồn kho
                            </button>
                            <button class="btn btn-sm btn-outline-primary mb-2 quick-action-btn" onclick="sendQuickMessage('Gợi ý sản phẩm nổi bật')">
                                <i class="fas fa-star me-1"></i>Sản phẩm nổi bật
                            </button>
                        </div>
                    </div>
                </div>
                <div class="ai-chat-input">
                    <div class="input-group">
                        <input type="text" class="form-control" id="aiChatInput" placeholder="Nhập câu hỏi..." 
                               onkeypress="if(event.key === 'Enter') sendMessage()">
                        <button class="btn btn-primary" onclick="sendMessage()" id="sendMessageBtn">
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
        const setNavbarOffset = () => {
            const navbar = document.querySelector('.navbar');
            if (!navbar) {
                return;
            }
            const height = navbar.offsetHeight;
            document.documentElement.style.setProperty('--navbar-height', `${height}px`);
        };

        window.addEventListener('load', setNavbarOffset);
        window.addEventListener('resize', setNavbarOffset);
        document.addEventListener('shown.bs.collapse', setNavbarOffset);
        document.addEventListener('hidden.bs.collapse', setNavbarOffset);

        // Initialize AOS
        AOS.init();

        // AI Chatbot functionality
        function toggleChat() {
            const chatWindow = document.getElementById('aiChatWindow');
            chatWindow.style.display = chatWindow.style.display === 'block' ? 'none' : 'block';
        }

        function sendQuickMessage(message) {
            const input = document.getElementById('aiChatInput');
            input.value = message;
            sendMessage();
        }

        function sendMessage() {
            const input = document.getElementById('aiChatInput');
            const message = input.value.trim();
            if (message) {
                addMessage('user', message);
                input.value = '';
                
                // Disable send button while processing
                const sendBtn = document.getElementById('sendMessageBtn');
                if (sendBtn) {
                    sendBtn.disabled = true;
                    sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                }
                
                // Hide quick actions after first message
                const quickActions = document.querySelector('.quick-actions');
                if (quickActions) {
                    quickActions.style.display = 'none';
                }
                
                // Show typing indicator
                const typingDiv = document.createElement('div');
                typingDiv.className = 'mb-3';
                typingDiv.id = 'typingIndicator';
                typingDiv.innerHTML = `
                    <div class="d-inline-block p-3 rounded-3 bg-light text-dark">
                        <i class="fas fa-robot me-2"></i>AI đang phân tích...
                    </div>
                `;
                document.getElementById('aiChatMessages').appendChild(typingDiv);
                
                // Call AI API
                fetchAIResponse(message).then(response => {
                    // Remove typing indicator
                    const typing = document.getElementById('typingIndicator');
                    if (typing) typing.remove();
                    
                    addMessage('ai', response);
                    
                    // Re-enable send button
                    if (sendBtn) {
                        sendBtn.disabled = false;
                        sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
                    }
                }).catch(error => {
                    // Remove typing indicator
                    const typing = document.getElementById('typingIndicator');
                    if (typing) typing.remove();
                    
                    addMessage('ai', 'Xin lỗi, tôi đang gặp sự cố. Vui lòng thử lại sau.');
                    
                    // Re-enable send button
                    if (sendBtn) {
                        sendBtn.disabled = false;
                        sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
                    }
                });
            }
        }

        async function fetchAIResponse(message) {
            const lowerMessage = message.toLowerCase();
            
            // Check for specific queries that need API calls
            if (lowerMessage.includes('còn hàng') || lowerMessage.includes('tồn kho')) {
                return await checkProductStock(message);
            }
            
            if (lowerMessage.includes('da') && (lowerMessage.includes('nên') || lowerMessage.includes('phù hợp'))) {
                return await getSkinRecommendations(message);
            }
            
            if (lowerMessage.includes('giá') || lowerMessage.includes('bao nhiêu')) {
                return await getPriceInfo(message);
            }
            
            // Default AI response
            return generateAIResponse(message);
        }

        async function checkProductStock(message) {
            try {
                // Extract product name from message
                const productName = extractProductName(message);
                if (productName) {
                    const response = await fetch(`/ai/stock-check?product_name=${encodeURIComponent(productName)}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await response.json();
                    
                    if (data.success && data.stock_info) {
                        let responseText = `📦 <strong>${data.stock_info.product_name}</strong><br><br>` +
                               `Tình trạng: <span class="badge bg-${data.stock_info.is_available ? 'success' : 'warning'}">${data.stock_info.status}</span><br>` +
                               `Số lượng: <strong>${data.stock_info.current_stock}</strong> sản phẩm<br><br>` +
                               `${data.stock_info.recommendation}`;
                        
                        if (data.stock_info.product_url) {
                            responseText += `<br><br><a href="${data.stock_info.product_url}" class="btn btn-sm btn-primary mt-2" target="_blank">Xem chi tiết sản phẩm <i class="fas fa-external-link-alt ms-1"></i></a>`;
                        }
                        
                        return responseText;
                    } else if (data.message) {
                        return data.message + (data.suggestion ? '<br>' + data.suggestion : '');
                    }
                }
                
                return 'Để kiểm tra tình trạng hàng chính xác, bạn có thể:<br>1. Xem trực tiếp trên <a href="/shop" target="_blank">trang Shop</a><br>2. Tìm kiếm sản phẩm cụ thể<br>3. Chat với chúng tôi để được tư vấn';
            } catch (error) {
                console.error('Error checking stock:', error);
                return 'Để kiểm tra tình trạng hàng, vui lòng xem trực tiếp trên <a href="/shop" target="_blank">trang Shop</a> hoặc liên hệ chúng tôi.';
            }
        }

        async function getSkinRecommendations(message) {
            try {
                // Extract skin type from message
                const skinType = extractSkinType(message);
                if (skinType) {
                    const response = await fetch(`/ai/skin-analysis?skin_type=${skinType}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await response.json();
                    
                    if (data.success && data.analysis) {
                        const analysis = data.analysis;
                        let responseText = `🎯 <strong>Tư vấn cho da ${skinType}</strong><br><br>`;
                        
                        if (analysis.recommendations) {
                            responseText += `<strong>Thành phần nên dùng:</strong><br>`;
                            analysis.recommendations.ingredients.forEach(ingredient => {
                                responseText += `• ${ingredient}<br>`;
                            });
                            responseText += `<br><strong>Thành phần nên tránh:</strong><br>`;
                            analysis.recommendations.avoid.forEach(item => {
                                responseText += `• ${item}<br>`;
                            });
                        }
                        
                        if (analysis.products && analysis.products.length > 0) {
                            responseText += `<br><strong>Sản phẩm phù hợp:</strong><br>`;
                            analysis.products.slice(0, 3).forEach(product => {
                                const productUrl = `/product/${product.id}`;
                                responseText += `• <a href="${productUrl}" target="_blank">${product.name}</a> - ${product.formatted_price || product.price}<br>`;
                            });
                            responseText += `<br><a href="/shop" class="btn btn-sm btn-primary" target="_blank">Xem tất cả sản phẩm <i class="fas fa-external-link-alt ms-1"></i></a>`;
                        }
                        
                        return responseText;
                    }
                }
                
                return generateAIResponse(message);
            } catch (error) {
                console.error('Error getting skin recommendations:', error);
                return generateAIResponse(message);
            }
        }

        async function getPriceInfo(message) {
            try {
                const productName = extractProductName(message);
                if (productName) {
                    // Search for product
                    const response = await fetch(`/shop?search=${encodeURIComponent(productName)}`);
                    // This would need to be implemented as an API endpoint
                    return `Giá sản phẩm được hiển thị rõ ràng trên từng trang sản phẩm. Bạn có thể:\n1. Xem giá trực tiếp trên website\n2. So sánh giá giữa các sản phẩm\n3. Liên hệ để được tư vấn về sản phẩm phù hợp ngân sách`;
                }
                
                return 'Giá sản phẩm được hiển thị rõ ràng trên từng trang sản phẩm. Bạn có thể xem giá trực tiếp trên website hoặc liên hệ để được tư vấn.';
            } catch (error) {
                return 'Giá sản phẩm được hiển thị trên từng trang sản phẩm. Bạn có thể so sánh giá và chọn sản phẩm phù hợp với ngân sách.';
            }
        }

        function extractProductName(message) {
            // Simple extraction - in real app, you'd use NLP
            const products = [
                'kem dưỡng ẩm', 'serum', 'sữa rửa mặt', 'kem chống nắng', 'mặt nạ',
                'kem nền', 'son môi', 'phấn phủ', 'nước hoa', 'dầu gội', 'serum tóc'
            ];
            
            for (const product of products) {
                if (message.toLowerCase().includes(product)) {
                    return product;
                }
            }
            return null;
        }

        function extractSkinType(message) {
            const skinTypes = {
                'da khô': 'dry',
                'da dầu': 'oily',
                'da hỗn hợp': 'combination',
                'da nhạy cảm': 'sensitive',
                'da thường': 'normal',
                'da mụn': 'acne-prone',
                'da trưởng thành': 'mature'
            };
            
            for (const [key, value] of Object.entries(skinTypes)) {
                if (message.toLowerCase().includes(key)) {
                    return value;
                }
            }
            return null;
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
            messageBubble.style.wordWrap = 'break-word';
            
            // Allow HTML content for links and formatting
            if (type === 'user') {
                messageBubble.textContent = message;
            } else {
                messageBubble.innerHTML = message;
            }
            
            messageDiv.appendChild(messageBubble);
            messagesContainer.appendChild(messageDiv);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        function generateAIResponse(message) {
            const lowerMessage = message.toLowerCase();
            const responses = {
                'da khô': 'Với làn da khô, tôi khuyên bạn nên sử dụng kem dưỡng ẩm có chứa Hyaluronic Acid và Ceramides. Sản phẩm phù hợp: Kem dưỡng ẩm chuyên sâu.<br><br><a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem sản phẩm cho da khô <i class="fas fa-external-link-alt ms-1"></i></a>',
                'da dầu': 'Làn da dầu cần sản phẩm kiểm soát bã nhờn. Tôi gợi ý: Sữa rửa mặt gel và kem dưỡng ẩm không gây nhờn.<br><br><a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem sản phẩm cho da dầu <i class="fas fa-external-link-alt ms-1"></i></a>',
                'da nhạy cảm': 'Da nhạy cảm cần sản phẩm dịu nhẹ. Hãy thử: Sữa rửa mặt dành cho da nhạy cảm và kem dưỡng ẩm phục hồi.<br><br><a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem sản phẩm cho da nhạy cảm <i class="fas fa-external-link-alt ms-1"></i></a>',
                'mụn': 'Để trị mụn hiệu quả, tôi khuyên: Sản phẩm chứa Salicylic Acid hoặc Benzoyl Peroxide.<br><br><a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem sản phẩm trị mụn <i class="fas fa-external-link-alt ms-1"></i></a>',
                'chống lão hóa': 'Sản phẩm chống lão hóa tốt nhất: Serum Vitamin C, Retinol và kem chống nắng SPF 50+.<br><br><a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem sản phẩm chống lão hóa <i class="fas fa-external-link-alt ms-1"></i></a>',
                'trang điểm': 'Để trang điểm đẹp tự nhiên: Kem nền phù hợp với tone da, phấn phủ và son môi.<br><br><a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem sản phẩm trang điểm <i class="fas fa-external-link-alt ms-1"></i></a>',
                'tẩy trang': 'Tẩy trang hiệu quả: Dầu tẩy trang hoặc nước tẩy trang dịu nhẹ.<br><br><a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem sản phẩm tẩy trang <i class="fas fa-external-link-alt ms-1"></i></a>',
                'còn hàng': 'Để kiểm tra tình trạng hàng, bạn có thể xem trực tiếp trên <a href="/shop" target="_blank">trang Shop</a> hoặc liên hệ với chúng tôi qua hotline.',
                'giá': 'Giá sản phẩm được hiển thị trên từng trang sản phẩm. Bạn có thể so sánh giá và chọn sản phẩm phù hợp với ngân sách.<br><br><a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem tất cả sản phẩm <i class="fas fa-external-link-alt ms-1"></i></a>',
                'giao hàng': 'Chúng tôi giao hàng toàn quốc với thời gian 2-5 ngày làm việc. Miễn phí ship cho đơn hàng từ 500k. Phí ship: 3,000 VNĐ/km, tối thiểu 10,000 VNĐ.',
                'đổi trả': 'Chính sách đổi trả trong 30 ngày nếu sản phẩm có vấn đề về chất lượng.',
                'thành phần': 'Thành phần được liệt kê chi tiết trên trang sản phẩm. Bạn có thể xem để kiểm tra phù hợp với làn da.',
                'hướng dẫn': 'Hướng dẫn sử dụng được cung cấp trên bao bì và trang sản phẩm. Nếu cần tư vấn thêm, hãy liên hệ chúng tôi.',
                'serum': 'Serum là sản phẩm chăm sóc da cô đặc. Tùy theo nhu cầu: Vitamin C (làm sáng), Hyaluronic Acid (dưỡng ẩm), Retinol (chống lão hóa).<br><br><a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem serum <i class="fas fa-external-link-alt ms-1"></i></a>',
                'kem dưỡng': 'Kem dưỡng ẩm nên chọn theo loại da: Da khô (dưỡng ẩm sâu), Da dầu (không gây nhờn), Da hỗn hợp (cân bằng).<br><br><a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem kem dưỡng <i class="fas fa-external-link-alt ms-1"></i></a>',
                'sữa rửa mặt': 'Sữa rửa mặt phù hợp: Da khô (dạng kem), Da dầu (dạng gel), Da nhạy cảm (không chứa hương liệu).<br><br><a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem sữa rửa mặt <i class="fas fa-external-link-alt ms-1"></i></a>',
                'mặt nạ': 'Mặt nạ nên dùng 2-3 lần/tuần. Loại phù hợp: Dưỡng ẩm, Làm sáng, Se khít lỗ chân lông.<br><br><a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem mặt nạ <i class="fas fa-external-link-alt ms-1"></i></a>',
                'chống nắng': 'Kem chống nắng SPF 30-50, thoa lại sau 2-3 giờ khi hoạt động ngoài trời.<br><br><a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem kem chống nắng <i class="fas fa-external-link-alt ms-1"></i></a>',
                'tẩy tế bào chết': 'Tẩy tế bào chết 1-2 lần/tuần. Chọn loại dịu nhẹ cho da nhạy cảm.<br><br><a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem sản phẩm tẩy tế bào chết <i class="fas fa-external-link-alt ms-1"></i></a>',
                'xịt khoáng': 'Xịt khoáng giúp cấp ẩm tức thì, có thể dùng nhiều lần trong ngày.<br><br><a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem xịt khoáng <i class="fas fa-external-link-alt ms-1"></i></a>',
                'tinh chất': 'Tinh chất chứa hoạt chất cô đặc, thường dùng trước kem dưỡng.<br><br><a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem tinh chất <i class="fas fa-external-link-alt ms-1"></i></a>',
                'phấn phủ': 'Phấn phủ giúp kiềm dầu và định hình lớp trang điểm.<br><br><a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem phấn phủ <i class="fas fa-external-link-alt ms-1"></i></a>',
                'son môi': 'Son môi nên chọn theo tone da và sự kiện. Có thể dưỡng môi trước khi thoa.<br><br><a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem son môi <i class="fas fa-external-link-alt ms-1"></i></a>',
                'phấn mắt': 'Phấn mắt có nhiều màu sắc, phù hợp với từng dịp và trang phục.<br><br><a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem phấn mắt <i class="fas fa-external-link-alt ms-1"></i></a>',
                'mascara': 'Mascara giúp làm dài và dày lông mi. Chọn loại không lem và dễ tẩy.<br><br><a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem mascara <i class="fas fa-external-link-alt ms-1"></i></a>',
                'nước hoa': 'Nước hoa có nhiều mùi hương khác nhau. Nên thử trước khi mua.',
                'dầu gội': 'Dầu gội nên chọn theo loại tóc: Khô, Dầu, Hỗn hợp, Nhuộm.',
                'dầu xả': 'Dầu xả giúp mềm mượt tóc, thoa từ giữa thân tóc đến ngọn.',
                'serum tóc': 'Serum tóc giúp phục hồi và bảo vệ tóc khỏi hư tổn.',
                'dụng cụ': 'Dụng cụ làm đẹp: Cọ trang điểm, Gương, Kẹp mi, Bông tẩy trang.'
            };

            const lowerMessage = message.toLowerCase();
            
            // Check for exact matches first
            for (const [key, response] of Object.entries(responses)) {
                if (lowerMessage.includes(key)) {
                    return response;
                }
            }

            // Check for product availability
            if (lowerMessage.includes('còn') && (lowerMessage.includes('hàng') || lowerMessage.includes('không'))) {
                return 'Để kiểm tra tình trạng hàng chính xác, bạn có thể:<br>1. Xem trực tiếp trên <a href="/shop" target="_blank">trang Shop</a><br>2. Tìm kiếm sản phẩm cụ thể<br>3. Chat với chúng tôi để được tư vấn cụ thể';
            }

            // Check for skin type recommendations
            if (lowerMessage.includes('da') && lowerMessage.includes('nên')) {
                return 'Dựa trên loại da của bạn, tôi gợi ý:<br>- Da khô: Kem dưỡng ẩm sâu, Serum Hyaluronic Acid<br>- Da dầu: Sữa rửa mặt gel, Kem dưỡng không gây nhờn<br>- Da hỗn hợp: Sản phẩm cân bằng<br>- Da nhạy cảm: Sản phẩm dịu nhẹ, không hương liệu<br><br><a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem tất cả sản phẩm <i class="fas fa-external-link-alt ms-1"></i></a>';
            }

            // Check for price inquiries
            if (lowerMessage.includes('giá') || lowerMessage.includes('bao nhiêu')) {
                return 'Giá sản phẩm được hiển thị rõ ràng trên từng trang sản phẩm. Bạn có thể:<br>1. Xem giá trực tiếp trên <a href="/shop" target="_blank">trang Shop</a><br>2. So sánh giá giữa các sản phẩm<br>3. Liên hệ để được tư vấn về sản phẩm phù hợp ngân sách';
            }

            // Default response with suggestions
            return 'Cảm ơn bạn đã hỏi! Tôi có thể tư vấn về:<br><br><strong>🔍 Tìm kiếm sản phẩm:</strong><br>- "còn hàng không", "giá bao nhiêu"<br><br><strong>👩‍⚕️ Tư vấn da:</strong><br>- "da khô", "da dầu", "da nhạy cảm"<br>- "mụn", "chống lão hóa", "dưỡng ẩm"<br><br><strong>💄 Sản phẩm cụ thể:</strong><br>- "serum", "kem dưỡng", "sữa rửa mặt"<br>- "trang điểm", "nước hoa", "chăm sóc tóc"<br><br><strong>🚚 Dịch vụ:</strong><br>- "giao hàng", "đổi trả", "hướng dẫn"<br><br>Bạn quan tâm đến vấn đề gì?<br><br><a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem tất cả sản phẩm <i class="fas fa-external-link-alt ms-1"></i></a>';
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
