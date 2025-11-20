@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <!-- Header -->
            <div class="text-center mb-4" data-aos="fade-down">
                <h1 class="display-4 fw-bold mb-3">
                    <i class="fas fa-robot text-primary me-3"></i>
                    BeautyAI Assistant
                </h1>
                <p class="lead text-muted">
                    Trợ lý tư vấn mỹ phẩm thông minh của bạn. Hỏi tôi bất cứ điều gì về sản phẩm, chăm sóc da, và làm đẹp!
                </p>
            </div>

            <!-- Chat Container -->
            <div class="card border-0 shadow-lg" data-aos="fade-up">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle me-3">
                                <i class="fas fa-robot fa-lg"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">BeautyAI Assistant</h5>
                                <small class="opacity-75" id="aiStatus">Đang hoạt động</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <!-- Mode Selection -->
                            <div class="btn-group" role="group">
                                <input type="radio" class="btn-check" name="chatMode" id="modeStandard" value="standard" checked>
                                <label class="btn btn-sm btn-light" for="modeStandard" title="Chế độ tiêu chuẩn">
                                    <i class="fas fa-comments me-1"></i>Tiêu chuẩn
                                </label>
                                
                                <input type="radio" class="btn-check" name="chatMode" id="modeGemini" value="gemini">
                                <label class="btn btn-sm btn-light" for="modeGemini" title="Chế độ Gemini AI">
                                    <i class="fas fa-brain me-1"></i>Gemini AI
                                </label>
                            </div>
                            <button class="btn btn-sm btn-light" onclick="clearChatHistory()" title="Xóa lịch sử chat">
                                <i class="fas fa-trash-alt me-1"></i>Xóa lịch sử
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <!-- Messages Container -->
                    <div class="ai-chat-messages-full" id="aiChatMessages">
                        <div class="welcome-message text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-spa fa-4x text-primary opacity-50"></i>
                            </div>
                            <h4 class="fw-semibold mb-3">Xin chào! Tôi là BeautyAI</h4>
                            <p class="text-muted mb-4">Trợ lý tư vấn mỹ phẩm của bạn. Hãy hỏi tôi về:</p>
                            
                            <!-- Quick Action Buttons -->
                            <div class="quick-actions-full d-flex flex-wrap justify-content-center gap-2 px-3">
                                <button class="btn btn-outline-primary quick-action-btn" onclick="sendQuickMessage('Tư vấn cho da khô')">
                                    <i class="fas fa-tint me-2"></i>Da khô
                                </button>
                                <button class="btn btn-outline-primary quick-action-btn" onclick="sendQuickMessage('Tư vấn cho da dầu')">
                                    <i class="fas fa-oil-can me-2"></i>Da dầu
                                </button>
                                <button class="btn btn-outline-primary quick-action-btn" onclick="sendQuickMessage('Tư vấn cho da nhạy cảm')">
                                    <i class="fas fa-heart me-2"></i>Da nhạy cảm
                                </button>
                                <button class="btn btn-outline-primary quick-action-btn" onclick="sendQuickMessage('Sản phẩm nào còn hàng?')">
                                    <i class="fas fa-box me-2"></i>Kiểm tra tồn kho
                                </button>
                                <button class="btn btn-outline-primary quick-action-btn" onclick="sendQuickMessage('Gợi ý sản phẩm nổi bật')">
                                    <i class="fas fa-star me-2"></i>Sản phẩm nổi bật
                                </button>
                                <button class="btn btn-outline-primary quick-action-btn" onclick="sendQuickMessage('Tư vấn về serum')">
                                    <i class="fas fa-flask me-2"></i>Serum
                                </button>
                                <button class="btn btn-outline-primary quick-action-btn" onclick="sendQuickMessage('Tư vấn về kem dưỡng ẩm')">
                                    <i class="fas fa-pump-soap me-2"></i>Kem dưỡng
                                </button>
                                <button class="btn btn-outline-primary quick-action-btn" onclick="sendQuickMessage('Tư vấn về chống lão hóa')">
                                    <i class="fas fa-sparkles me-2"></i>Chống lão hóa
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Input Container -->
                    <div class="ai-chat-input-full border-top p-3 bg-light">
                        <form id="chatForm" onsubmit="return false;">
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control form-control-lg" 
                                       id="aiChatInput" 
                                       placeholder="Nhập câu hỏi của bạn..." 
                                       autocomplete="off">
                                <button class="btn btn-primary btn-lg" type="button" onclick="sendMessage()" id="sendMessageBtn">
                                    <i class="fas fa-paper-plane me-2"></i>Gửi
                                </button>
                            </div>
                            <small class="text-muted mt-2 d-block">
                                <i class="fas fa-info-circle me-1"></i>
                                Nhấn Enter để gửi tin nhắn
                            </small>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tips Section -->
            <div class="row mt-4">
                <div class="col-md-4 mb-3" data-aos="fade-up" data-aos-delay="100">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-lightbulb fa-2x text-warning"></i>
                            </div>
                            <h6 class="fw-semibold">Mẹo sử dụng</h6>
                            <p class="text-muted small mb-0">Hỏi cụ thể về loại da, sản phẩm hoặc vấn đề bạn đang gặp phải</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3" data-aos="fade-up" data-aos-delay="200">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-history fa-2x text-info"></i>
                            </div>
                            <h6 class="fw-semibold">Lịch sử chat</h6>
                            <p class="text-muted small mb-0">Lịch sử chat của bạn được lưu tự động và sẽ được khôi phục khi quay lại</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3" data-aos="fade-up" data-aos-delay="300">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-shopping-bag fa-2x text-success"></i>
                            </div>
                            <h6 class="fw-semibold">Mua sắm ngay</h6>
                            <p class="text-muted small mb-0">Tìm thấy sản phẩm phù hợp? Click vào link để xem chi tiết và mua ngay</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.ai-chat-messages-full {
    height: 600px;
    overflow-y: auto;
    padding: 2rem;
    background: linear-gradient(180deg, #fff 0%, #f8f9fa 100%);
}

.welcome-message {
    max-width: 600px;
    margin: 0 auto;
}

.quick-actions-full .quick-action-btn {
    border-radius: 25px;
    padding: 0.5rem 1.25rem;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.quick-actions-full .quick-action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 95, 167, 0.3);
}

.ai-chat-input-full {
    border-radius: 0 0 20px 20px;
}

.ai-chat-input-full .form-control {
    border-radius: 25px 0 0 25px;
    border: 2px solid rgba(255, 95, 167, 0.2);
    padding: 0.75rem 1.5rem;
}

.ai-chat-input-full .form-control:focus {
    border-color: var(--pink-500);
    box-shadow: 0 0 0 0.2rem rgba(255, 95, 167, 0.15);
}

.ai-chat-input-full .btn {
    border-radius: 0 25px 25px 0;
    padding: 0.75rem 2rem;
}

.message-bubble {
    max-width: 75%;
    margin-bottom: 1.5rem;
    animation: fadeIn 0.3s ease;
}

.message-bubble.user {
    margin-left: auto;
}

.message-bubble.user .bubble-content {
    background: var(--gradient-primary);
    color: #fff;
    border-radius: 20px 20px 5px 20px;
    padding: 1rem 1.5rem;
}

.message-bubble.ai .bubble-content {
    background: #fff;
    color: var(--gray-900);
    border: 1px solid rgba(255, 95, 167, 0.1);
    border-radius: 20px 20px 20px 5px;
    padding: 1rem 1.5rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.message-bubble .message-time {
    font-size: 0.75rem;
    opacity: 0.6;
    margin-top: 0.5rem;
}

.avatar-circle {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
}

.feature-icon {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: rgba(255, 95, 167, 0.1);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Scrollbar styling */
.ai-chat-messages-full::-webkit-scrollbar {
    width: 8px;
}

.ai-chat-messages-full::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.ai-chat-messages-full::-webkit-scrollbar-thumb {
    background: var(--pink-300);
    border-radius: 10px;
}

.ai-chat-messages-full::-webkit-scrollbar-thumb:hover {
    background: var(--pink-500);
}

/* Mode Selection Styles */
.btn-group .btn-check:checked + .btn {
    background: rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.5);
    color: #fff;
    font-weight: 600;
}

.btn-group .btn-check + .btn {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.3);
    color: rgba(255, 255, 255, 0.9);
    transition: all 0.3s ease;
}

.btn-group .btn-check + .btn:hover {
    background: rgba(255, 255, 255, 0.2);
    color: #fff;
}
</style>

<script>
// Load chat history from database
async function loadChatHistory() {
    try {
        const response = await fetch('{{ route("ai.chat.history") }}', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        
        if (data.success && data.history && data.history.length > 0) {
            const messagesContainer = document.getElementById('aiChatMessages');
            
            // Clear welcome message
            const welcomeMsg = messagesContainer.querySelector('.welcome-message');
            if (welcomeMsg) {
                welcomeMsg.remove();
            }
            
            // Restore messages
            data.history.forEach(msg => {
                addMessageToContainer(msg.type, msg.content, msg.timestamp, false);
            });
            
            // Restore last mode
            if (data.lastMode) {
                const modeRadio = document.querySelector(`input[name="chatMode"][value="${data.lastMode}"]`);
                if (modeRadio) {
                    modeRadio.checked = true;
                    updateAIStatus();
                }
            }
            
            // Scroll to bottom
            setTimeout(() => {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }, 100);
        }
    } catch (e) {
        console.error('Error loading chat history:', e);
    }
}

// Clear chat history
async function clearChatHistory() {
    if (confirm('Bạn có chắc muốn xóa toàn bộ lịch sử chat?')) {
        try {
            const response = await fetch('{{ route("ai.chat.clear") }}', {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const data = await response.json();
            if (data.success) {
                location.reload();
            }
        } catch (e) {
            console.error('Error clearing chat history:', e);
        if (window.notify) window.notify({ type: 'error', title: 'Lỗi', message: 'Có lỗi xảy ra khi xóa lịch sử chat.', duration: 3000 });
        }
    }
}

// Add message to container
function addMessageToContainer(type, message, timestamp, save = false) {
    const messagesContainer = document.getElementById('aiChatMessages');
    
    if (!messagesContainer) {
        console.error('Messages container not found');
        return;
    }
    
    // Remove welcome message if exists
    const welcomeMsg = messagesContainer.querySelector('.welcome-message');
    if (welcomeMsg) {
        welcomeMsg.remove();
    }
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `message-bubble ${type}`;
    
    const bubbleContent = document.createElement('div');
    bubbleContent.className = 'bubble-content';
    
    if (type === 'user') {
        bubbleContent.textContent = message;
    } else {
        bubbleContent.innerHTML = message;
    }
    
    messageDiv.appendChild(bubbleContent);
    
    // Add timestamp
    if (timestamp) {
        const timeDiv = document.createElement('div');
        timeDiv.className = 'message-time text-end';
        const date = new Date(timestamp);
        timeDiv.textContent = date.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
        messageDiv.appendChild(timeDiv);
    }
    
    messagesContainer.appendChild(messageDiv);
    
    // Scroll to bottom with delay to ensure DOM is updated
    setTimeout(() => {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }, 50);
}

// Send quick message
function sendQuickMessage(message) {
    document.getElementById('aiChatInput').value = message;
    sendMessage();
}

// Send message
function sendMessage() {
    const input = document.getElementById('aiChatInput');
    const message = input.value.trim();
    
    if (!message) return;
    
    // Add user message
    addMessageToContainer('user', message);
    input.value = '';
    
    // Disable send button
    const sendBtn = document.getElementById('sendMessageBtn');
    sendBtn.disabled = true;
    sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang gửi...';
    
    // Show typing indicator
    const typingDiv = document.createElement('div');
    typingDiv.className = 'message-bubble ai';
    typingDiv.id = 'typingIndicator';
    typingDiv.innerHTML = `
        <div class="bubble-content">
            <i class="fas fa-robot me-2"></i>AI đang phân tích...
        </div>
    `;
    document.getElementById('aiChatMessages').appendChild(typingDiv);
    document.getElementById('aiChatMessages').scrollTop = document.getElementById('aiChatMessages').scrollHeight;
    
    // Call AI API
    fetchAIResponse(message).then(response => {
        // Remove typing indicator
        const typing = document.getElementById('typingIndicator');
        if (typing) typing.remove();
        
        addMessageToContainer('ai', response);
        
        // Re-enable send button
        sendBtn.disabled = false;
        sendBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Gửi';
    }).catch(error => {
        // Remove typing indicator
        const typing = document.getElementById('typingIndicator');
        if (typing) typing.remove();
        
        addMessageToContainer('ai', 'Xin lỗi, tôi đang gặp sự cố. Vui lòng thử lại sau.');
        
        // Re-enable send button
        sendBtn.disabled = false;
        sendBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Gửi';
    });
}

// Get current chat mode
function getChatMode() {
    const modeRadio = document.querySelector('input[name="chatMode"]:checked');
    return modeRadio ? modeRadio.value : 'standard';
}

// Fetch AI response
async function fetchAIResponse(message) {
    const mode = getChatMode();
    
    // If Gemini mode is selected, directly use Gemini API only
    if (mode === 'gemini') {
        return await fetchGeminiResponse(message, mode);
    }
    
    // Standard mode - use backend API
    return await fetchStandardResponse(message, mode);
}

// Fetch standard mode response
async function fetchStandardResponse(message, mode) {
    try {
        const response = await fetch('{{ route("ai.chat.standard") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                message: message,
                mode: mode
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            return data.message;
        } else {
            return data.message || 'Xin lỗi, tôi đang gặp sự cố. Vui lòng thử lại sau.';
        }
    } catch (error) {
        console.error('Error fetching standard response:', error);
        return 'Xin lỗi, tôi đang gặp sự cố kết nối. Vui lòng thử lại sau.';
    }
}

// Fetch response from Gemini API
async function fetchGeminiResponse(message, mode) {
    try {
        const response = await fetch('{{ route("ai.chat.gemini") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                message: message,
                mode: mode
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            return data.message;
        } else {
            return data.message || 'Xin lỗi, tôi đang gặp sự cố. Vui lòng thử lại sau.';
        }
    } catch (error) {
        console.error('Error fetching Gemini response:', error);
        return 'Xin lỗi, tôi đang gặp sự cố kết nối. Vui lòng thử lại sau.';
    }
}

// Strip HTML tags for Gemini API
function stripHtmlTags(html) {
    const tmp = document.createElement('DIV');
    tmp.innerHTML = html;
    return tmp.textContent || tmp.innerText || '';
}

async function checkProductStock(message) {
    try {
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
            return `Giá sản phẩm được hiển thị rõ ràng trên từng trang sản phẩm. Bạn có thể:<br>1. Xem giá trực tiếp trên <a href="/shop" target="_blank">trang Shop</a><br>2. So sánh giá giữa các sản phẩm<br>3. Liên hệ để được tư vấn về sản phẩm phù hợp ngân sách`;
        }
        
        return 'Giá sản phẩm được hiển thị trên từng trang sản phẩm. Bạn có thể so sánh giá và chọn sản phẩm phù hợp với ngân sách.';
    } catch (error) {
        return 'Giá sản phẩm được hiển thị trên từng trang sản phẩm. Bạn có thể so sánh giá và chọn sản phẩm phù hợp với ngân sách.';
    }
}

function extractProductName(message) {
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

function generateAIResponse(message) {
    const lowerMessage = message.toLowerCase();
    const responses = {
        'da khô': 'Với làn da khô, tôi khuyên bạn nên sử dụng kem dưỡng ẩm có chứa Hyaluronic Acid và Ceramides. Sản phẩm phù hợp: Kem dưỡng ẩm chuyên sâu.<br><br><a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem sản phẩm cho da khô <i class="fas fa-external-link-alt ms-1"></i></a>',
        'da dầu': 'Làn da dầu cần sản phẩm kiểm soát bã nhờn. Tôi gợi ý: Sữa rửa mặt gel và kem dưỡng ẩm không gây nhờn.<br><br><a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem sản phẩm cho da dầu <i class="fas fa-external-link-alt ms-1"></i></a>',
        'da nhạy cảm': 'Da nhạy cảm cần sản phẩm dịu nhẹ. Hãy thử: Sữa rửa mặt dành cho da nhạy cảm và kem dưỡng ẩm phục hồi.<br><br><a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem sản phẩm cho da nhạy cảm <i class="fas fa-external-link-alt ms-1"></i></a>',
        'mụn': 'Để trị mụn hiệu quả, tôi khuyên: Sản phẩm chứa Salicylic Acid hoặc Benzoyl Peroxide.<br><br><a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem sản phẩm trị mụn <i class="fas fa-external-link-alt ms-1"></i></a>',
        'chống lão hóa': 'Sản phẩm chống lão hóa tốt nhất: Serum Vitamin C, Retinol và kem chống nắng SPF 50+.<br><br><a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem sản phẩm chống lão hóa <i class="fas fa-external-link-alt ms-1"></i></a>',
        'serum': 'Serum là sản phẩm chăm sóc da cô đặc. Tùy theo nhu cầu: Vitamin C (làm sáng), Hyaluronic Acid (dưỡng ẩm), Retinol (chống lão hóa).<br><br><a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem serum <i class="fas fa-external-link-alt ms-1"></i></a>',
        'kem dưỡng': 'Kem dưỡng ẩm nên chọn theo loại da: Da khô (dưỡng ẩm sâu), Da dầu (không gây nhờn), Da hỗn hợp (cân bằng).<br><br><a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem kem dưỡng <i class="fas fa-external-link-alt ms-1"></i></a>',
    };

    // Check for exact matches first
    for (const [key, response] of Object.entries(responses)) {
        if (lowerMessage.includes(key)) {
            return response;
        }
    }

    // Default response
    return 'Cảm ơn bạn đã hỏi! Tôi có thể tư vấn về:<br><br><strong>🔍 Tìm kiếm sản phẩm:</strong><br>- "còn hàng không", "giá bao nhiêu"<br><br><strong>👩‍⚕️ Tư vấn da:</strong><br>- "da khô", "da dầu", "da nhạy cảm"<br>- "mụn", "chống lão hóa", "dưỡng ẩm"<br><br><strong>💄 Sản phẩm cụ thể:</strong><br>- "serum", "kem dưỡng", "sữa rửa mặt"<br><br>Bạn quan tâm đến vấn đề gì?<br><br><a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem tất cả sản phẩm <i class="fas fa-external-link-alt ms-1"></i></a>';
}

// Update status based on mode
function updateAIStatus() {
    const mode = getChatMode();
    const statusEl = document.getElementById('aiStatus');
    if (statusEl) {
        if (mode === 'gemini') {
            statusEl.textContent = 'Gemini AI - Đang hoạt động';
            statusEl.innerHTML = '<i class="fas fa-brain me-1"></i>Gemini AI - Đang hoạt động';
        } else {
            statusEl.textContent = 'Đang hoạt động';
            statusEl.innerHTML = 'Đang hoạt động';
        }
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Load chat history first
    loadChatHistory();
    
    // Update status
    updateAIStatus();
    
    // Mode change listener
    const modeRadios = document.querySelectorAll('input[name="chatMode"]');
    modeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            updateAIStatus();
        });
    });
    
    // Enter key to send message
    const aiChatInput = document.getElementById('aiChatInput');
    if (aiChatInput) {
        aiChatInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    }
});
</script>
@endsection

