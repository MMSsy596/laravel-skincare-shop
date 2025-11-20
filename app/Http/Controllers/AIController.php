<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\Review;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIController extends Controller
{
    public function chat()
    {
        // Get chat history from database
        $chatHistory = $this->getChatHistory();
        $lastMode = $this->getLastChatMode();
        
        return view('ai.chat', [
            'chatHistory' => $chatHistory,
            'lastMode' => $lastMode
        ]);
    }

    /**
     * Get chat history from database
     */
    private function getChatHistory()
    {
        $userId = auth()->id();
        $sessionId = ChatMessage::getSessionId();
        
        $query = ChatMessage::orderBy('created_at', 'asc');
        
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }
        
        return $query->get()->map(function($msg) {
            return [
                'type' => $msg->type,
                'content' => $msg->message,
                'timestamp' => $msg->created_at->toISOString(),
                'mode' => $msg->mode
            ];
        });
    }

    /**
     * Get last chat mode used
     */
    private function getLastChatMode()
    {
        $userId = auth()->id();
        $sessionId = ChatMessage::getSessionId();
        
        $query = ChatMessage::orderBy('created_at', 'desc');
        
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }
        
        $lastMessage = $query->first();
        return $lastMessage ? $lastMessage->mode : 'standard';
    }

    /**
     * Save message to database
     */
    private function saveMessage($type, $message, $mode = 'standard')
    {
        $userId = auth()->id();
        $sessionId = ChatMessage::getSessionId();
        
        return ChatMessage::create([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'type' => $type,
            'message' => $message,
            'mode' => $mode,
        ]);
    }

    /**
     * Get chat history for API
     */
    public function getChatHistoryApi()
    {
        $history = $this->getChatHistory();
        $lastMode = $this->getLastChatMode();
        
        return response()->json([
            'success' => true,
            'history' => $history,
            'lastMode' => $lastMode
        ]);
    }

    /**
     * Clear chat history
     */
    public function clearChatHistory()
    {
        $userId = auth()->id();
        $sessionId = ChatMessage::getSessionId();
        
        $query = ChatMessage::query();
        
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }
        
        $query->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Đã xóa lịch sử chat'
        ]);
    }

    public function chatWithGemini(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'mode' => 'required|string|in:standard,gemini',
        ]);

        $apiKey = config('services.gemini.api_key');
        $mode = $request->input('mode', 'gemini');
        $message = $request->input('message');
        
        // Save user message to database
        $this->saveMessage('user', $message, $mode);
        
        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Gemini API key chưa được cấu hình'
            ], 500);
        }

        try {
            // Get chat history from database
            $history = $this->getChatHistory();
            
            // Build conversation history for Gemini (last 10 messages)
            $contents = [];
            $recentHistory = array_slice($history->toArray(), -10);
            
            foreach ($recentHistory as $msg) {
                $role = $msg['type'] === 'user' ? 'user' : 'model';
                $contents[] = [
                    'role' => $role,
                    'parts' => [['text' => strip_tags($msg['content'])]]
                ];
            }
            
            // Add current message
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $message]]
            ];

            // System instruction for beauty/skincare context
            $systemInstruction = "Bạn là BeautyAI, một trợ lý tư vấn mỹ phẩm và chăm sóc da chuyên nghiệp, thân thiện và nhiệt tình. 
            Nhiệm vụ của bạn là tư vấn về:
            - Các sản phẩm mỹ phẩm phù hợp với từng loại da
            - Chăm sóc da hàng ngày
            - Thành phần mỹ phẩm và công dụng
            - Quy trình skincare
            - Trang điểm và làm đẹp
            - Dịch vụ giao hàng, đổi trả
            
            Khi người dùng chào hỏi (hi, hello, xin chào, chào bạn), hãy chào lại một cách thân thiện và giới thiệu ngắn gọn về khả năng của bạn.
            Hãy trả lời một cách thân thiện, chuyên nghiệp và hữu ích. 
            Nếu được hỏi về sản phẩm cụ thể, hãy đề xuất các sản phẩm phù hợp.
            Luôn trả lời bằng tiếng Việt với giọng điệu thân thiện, gần gũi.";

            $response = Http::timeout(30)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key={$apiKey}", [
                'contents' => $contents,
                'systemInstruction' => [
                    'parts' => [['text' => $systemInstruction]]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 1024,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $aiResponse = $data['candidates'][0]['content']['parts'][0]['text'];
                    
                    // Convert markdown-like formatting to HTML
                    $aiResponse = nl2br($aiResponse);
                    $aiResponse = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $aiResponse);
                    $aiResponse = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $aiResponse);
                    
                    // Save AI response to database
                    $this->saveMessage('ai', $aiResponse, $mode);
                    
                    return response()->json([
                        'success' => true,
                        'message' => $aiResponse
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Không thể nhận phản hồi từ AI. Vui lòng thử lại sau.'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Gemini API Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi kết nối với AI. Vui lòng thử lại sau.'
            ], 500);
        }
    }

    /**
     * Handle standard mode chat
     */
    public function chatStandard(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'mode' => 'required|string|in:standard,gemini',
        ]);

        $message = $request->input('message');
        $mode = $request->input('mode', 'standard');
        
        // Save user message to database
        $this->saveMessage('user', $message, $mode);
        
        $lowerMessage = strtolower($message);
        $response = '';
        
        // Check for specific queries that need API calls
        if (strpos($lowerMessage, 'còn hàng') !== false || strpos($lowerMessage, 'tồn kho') !== false) {
            // Handle stock check
            $productName = $this->extractProductName($message);
            if ($productName) {
                $product = Product::where('name', 'like', '%' . $productName . '%')->first();
                if ($product) {
                    $response = $this->formatStockResponse($product);
                } else {
                    $response = 'Không tìm thấy sản phẩm. Bạn có thể tìm kiếm sản phẩm trên <a href="/shop" target="_blank">trang Shop</a>.';
                }
            } else {
                $response = 'Để kiểm tra tình trạng hàng chính xác, bạn có thể xem trực tiếp trên <a href="/shop" target="_blank">trang Shop</a>.';
            }
        } elseif (strpos($lowerMessage, 'da') !== false && (strpos($lowerMessage, 'nên') !== false || strpos($lowerMessage, 'phù hợp') !== false)) {
            // Handle skin recommendations
            $skinType = $this->extractSkinType($message);
            if ($skinType) {
                $response = $this->formatSkinRecommendation($skinType);
            } else {
                $response = $this->generateStandardResponse($message);
            }
        } else {
            $response = $this->generateStandardResponse($message);
        }
        
        // Save AI response to database
        $this->saveMessage('ai', $response, $mode);
        
        return response()->json([
            'success' => true,
            'message' => $response
        ]);
    }

    private function extractProductName($message)
    {
        $products = [
            'kem dưỡng ẩm', 'serum', 'sữa rửa mặt', 'kem chống nắng', 'mặt nạ',
            'kem nền', 'son môi', 'phấn phủ', 'nước hoa', 'dầu gội', 'serum tóc'
        ];
        
        foreach ($products as $product) {
            if (stripos($message, $product) !== false) {
                return $product;
            }
        }
        return null;
    }

    private function extractSkinType($message)
    {
        $skinTypes = [
            'da khô' => 'dry',
            'da dầu' => 'oily',
            'da hỗn hợp' => 'combination',
            'da nhạy cảm' => 'sensitive',
            'da thường' => 'normal',
            'da mụn' => 'acne-prone',
            'da trưởng thành' => 'mature'
        ];
        
        foreach ($skinTypes as $key => $value) {
            if (stripos($message, $key) !== false) {
                return $value;
            }
        }
        return null;
    }

    private function formatStockResponse($product)
    {
        $status = $product->stock > 0 ? 'success' : 'warning';
        $statusText = $product->stock > 0 ? 'Còn hàng' : 'Hết hàng';
        
        return "📦 <strong>{$product->name}</strong><br><br>" .
               "Tình trạng: <span class=\"badge bg-{$status}\">{$statusText}</span><br>" .
               "Số lượng: <strong>{$product->stock}</strong> sản phẩm<br><br>" .
               "<a href=\"/product/{$product->id}\" class=\"btn btn-sm btn-primary mt-2\" target=\"_blank\">Xem chi tiết sản phẩm <i class=\"fas fa-external-link-alt ms-1\"></i></a>";
    }

    private function formatSkinRecommendation($skinType)
    {
        $recommendations = $this->getSkinTypeRecommendations($skinType);
        $products = $this->getProductsForSkinType($skinType);
        
        $response = "🎯 <strong>Tư vấn cho da {$skinType}</strong><br><br>";
        
        if ($recommendations) {
            $response .= "<strong>Thành phần nên dùng:</strong><br>";
            foreach ($recommendations['ingredients'] as $ingredient) {
                $response .= "• {$ingredient}<br>";
            }
            $response .= "<br><strong>Thành phần nên tránh:</strong><br>";
            foreach ($recommendations['avoid'] as $item) {
                $response .= "• {$item}<br>";
            }
        }
        
        if ($products && $products->count() > 0) {
            $response .= "<br><strong>Sản phẩm phù hợp:</strong><br>";
            foreach ($products->take(3) as $product) {
                $response .= "• <a href=\"/product/{$product->id}\" target=\"_blank\">{$product->name}</a> - " . number_format($product->price) . " VNĐ<br>";
            }
            $response .= "<br><a href=\"/shop\" class=\"btn btn-sm btn-primary\" target=\"_blank\">Xem tất cả sản phẩm <i class=\"fas fa-external-link-alt ms-1\"></i></a>";
        }
        
        return $response;
    }

    private function generateStandardResponse($message)
    {
        $lowerMessage = trim(strtolower($message));
        
        // Handle greetings
        $greetings = ['hi', 'hello', 'xin chào', 'chào', 'chào bạn', 'hey', 'hế lô'];
        foreach ($greetings as $greeting) {
            if ($lowerMessage === $greeting || $lowerMessage === $greeting . '!') {
                return 'Xin chào! 👋 Tôi là BeautyAI, trợ lý tư vấn mỹ phẩm của bạn. Tôi có thể giúp bạn:<br><br>' .
                       '• Tìm kiếm và kiểm tra sản phẩm<br>' .
                       '• Tư vấn về chăm sóc da<br>' .
                       '• Gợi ý sản phẩm phù hợp với loại da của bạn<br>' .
                       '• Trả lời các câu hỏi về làm đẹp<br><br>' .
                       'Bạn muốn tìm hiểu về điều gì hôm nay? 😊<br><br>' .
                       '<a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem sản phẩm <i class="fas fa-external-link-alt ms-1"></i></a>';
            }
        }
        
        $responses = [
            'da khô' => 'Với làn da khô, tôi khuyên bạn nên sử dụng kem dưỡng ẩm có chứa Hyaluronic Acid và Ceramides. Sản phẩm phù hợp: Kem dưỡng ẩm chuyên sâu.<br><br><a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem sản phẩm cho da khô <i class="fas fa-external-link-alt ms-1"></i></a>',
            'da dầu' => 'Làn da dầu cần sản phẩm kiểm soát bã nhờn. Tôi gợi ý: Sữa rửa mặt gel và kem dưỡng ẩm không gây nhờn.<br><br><a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem sản phẩm cho da dầu <i class="fas fa-external-link-alt ms-1"></i></a>',
            'da nhạy cảm' => 'Da nhạy cảm cần sản phẩm dịu nhẹ. Hãy thử: Sữa rửa mặt dành cho da nhạy cảm và kem dưỡng ẩm phục hồi.<br><br><a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem sản phẩm cho da nhạy cảm <i class="fas fa-external-link-alt ms-1"></i></a>',
            'mụn' => 'Để trị mụn hiệu quả, tôi khuyên: Sản phẩm chứa Salicylic Acid hoặc Benzoyl Peroxide.<br><br><a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem sản phẩm trị mụn <i class="fas fa-external-link-alt ms-1"></i></a>',
            'chống lão hóa' => 'Sản phẩm chống lão hóa tốt nhất: Serum Vitamin C, Retinol và kem chống nắng SPF 50+.<br><br><a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem sản phẩm chống lão hóa <i class="fas fa-external-link-alt ms-1"></i></a>',
            'serum' => 'Serum là sản phẩm chăm sóc da cô đặc. Tùy theo nhu cầu: Vitamin C (làm sáng), Hyaluronic Acid (dưỡng ẩm), Retinol (chống lão hóa).<br><br><a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem serum <i class="fas fa-external-link-alt ms-1"></i></a>',
            'kem dưỡng' => 'Kem dưỡng ẩm nên chọn theo loại da: Da khô (dưỡng ẩm sâu), Da dầu (không gây nhờn), Da hỗn hợp (cân bằng).<br><br><a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem kem dưỡng <i class="fas fa-external-link-alt ms-1"></i></a>',
        ];

        foreach ($responses as $key => $response) {
            if (strpos($lowerMessage, $key) !== false) {
                return $response;
            }
        }

        return 'Cảm ơn bạn đã hỏi! Tôi có thể tư vấn về:<br><br><strong>🔍 Tìm kiếm sản phẩm:</strong><br>- "còn hàng không", "giá bao nhiêu"<br><br><strong>👩‍⚕️ Tư vấn da:</strong><br>- "da khô", "da dầu", "da nhạy cảm"<br>- "mụn", "chống lão hóa", "dưỡng ẩm"<br><br><strong>💄 Sản phẩm cụ thể:</strong><br>- "serum", "kem dưỡng", "sữa rửa mặt"<br>- "trang điểm", "nước hoa", "chăm sóc tóc"<br><br><strong>🚚 Dịch vụ:</strong><br>- "giao hàng", "đổi trả", "hướng dẫn"<br><br>Bạn quan tâm đến vấn đề gì?<br><br><a href="/shop" class="btn btn-sm btn-primary mt-2" target="_blank">Xem tất cả sản phẩm <i class="fas fa-external-link-alt ms-1"></i></a>';
    }

    public function getRecommendations(Request $request)
    {
        $userPreferences = $request->only(['skin_type', 'age_group', 'category', 'concerns', 'budget']);
        
        $query = Product::with('reviews')->active();
        
        // Filter by skin type
        if (!empty($userPreferences['skin_type'])) {
            $query->where('skin_type', $userPreferences['skin_type']);
        }
        
        // Filter by age group
        if (!empty($userPreferences['age_group'])) {
            $query->where('age_group', $userPreferences['age_group']);
        }
        
        // Filter by category
        if (!empty($userPreferences['category'])) {
            $query->where('category', $userPreferences['category']);
        }
        
        // Filter by budget
        if (!empty($userPreferences['budget'])) {
            $budget = (float) $userPreferences['budget'];
            $query->where('price', '<=', $budget);
        }
        
        // Filter by concerns
        if (!empty($userPreferences['concerns'])) {
            $concerns = explode(',', $userPreferences['concerns']);
            foreach ($concerns as $concern) {
                $concern = trim(strtolower($concern));
                $query->where(function($q) use ($concern) {
                    $q->where('name', 'like', "%{$concern}%")
                      ->orWhere('description', 'like', "%{$concern}%")
                      ->orWhere('ingredients', 'like', "%{$concern}%");
                });
            }
        }
        
            $recommendations = $query->withAvg('reviews', 'rating')
                                ->withCount('reviews')
                                ->orderBy('reviews_avg_rating', 'desc')
                                ->orderBy('reviews_count', 'desc')
                                ->limit(6)
                                ->get();
        
        return response()->json([
            'success' => true,
            'recommendations' => $recommendations,
            'message' => 'Đây là những sản phẩm phù hợp với nhu cầu của bạn'
        ]);
    }

    public function checkStock(Request $request)
    {
        $productId = $request->product_id;
        $productName = $request->product_name;
        
        $product = null;
        
        // Tìm sản phẩm theo ID hoặc tên
        if ($productId) {
            $product = Product::find($productId);
        } elseif ($productName) {
            $product = Product::where('name', 'like', '%' . $productName . '%')
                ->orWhere('name', 'like', '%' . str_replace(' ', '%', $productName) . '%')
                ->first();
        }
        
        if (!$product) {
            // Nếu không tìm thấy sản phẩm cụ thể, trả về thông tin tổng quan
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm. Bạn có thể tìm kiếm sản phẩm trên trang Shop.',
                'suggestion' => 'Hãy thử tìm kiếm với từ khóa khác hoặc xem danh sách sản phẩm tại /shop'
            ]);
        }
        
        $stockInfo = [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'current_stock' => $product->stock,
            'status' => $product->stock_status,
            'is_available' => $product->is_in_stock,
            'recommendation' => $this->getStockRecommendation($product),
            'product_url' => route('product.show', $product->id)
        ];
        
        return response()->json([
            'success' => true,
            'stock_info' => $stockInfo
        ]);
    }

    public function getSkinAnalysis(Request $request)
    {
        $skinType = $request->skin_type;
        $concerns = $request->concerns;
        
        $analysis = [
            'skin_type' => $skinType,
            'recommendations' => $this->getSkinTypeRecommendations($skinType),
            'products' => $this->getProductsForSkinType($skinType, $concerns),
            'routine' => $this->getSkincareRoutine($skinType),
            'tips' => $this->getSkincareTips($skinType)
        ];
        
        return response()->json([
            'success' => true,
            'analysis' => $analysis
        ]);
    }

    public function getProductAnalysis(Request $request)
    {
        $productId = $request->product_id;
        $product = Product::with('reviews')->find($productId);
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm'
            ]);
        }
        
        $analysis = [
            'product' => $product,
            'ai_analysis' => $product->getAiAnalysis(),
            'similar_products' => $product->getSimilarProducts(),
            'popular_combinations' => $this->getPopularCombinations($product),
            'usage_tips' => $this->getUsageTips($product),
            'ingredient_analysis' => $this->analyzeIngredients($product->ingredients)
        ];
        
        return response()->json([
            'success' => true,
            'analysis' => $analysis
        ]);
    }

    public function getTrendingProducts()
    {
        // Get trending products based on sales and reviews
        $trendingProducts = Product::with('reviews')
            ->active()
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->whereHas('reviews', function($query) {
                $query->where('created_at', '>=', now()->subDays(30));
            })
            ->orderBy('reviews_count', 'desc')
            ->orderBy('reviews_avg_rating', 'desc')
            ->limit(10)
            ->get();
        
        return response()->json([
            'success' => true,
            'trending_products' => $trendingProducts
        ]);
    }

    public function getPersonalizedRecommendations(Request $request)
    {
        $userId = auth()->id();
        
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để nhận gợi ý cá nhân hóa'
            ]);
        }
        
        // Get user's purchase history
        $purchaseHistory = Order::where('user_id', $userId)
            ->with('orderItems.product')
            ->get()
            ->pluck('orderItems.*.product.category')
            ->flatten()
            ->countBy();
        
        // Get user's reviews
        $userReviews = Review::where('user_id', $userId)
            ->with('product')
            ->get();
        
        // Analyze preferences
        $preferences = $this->analyzeUserPreferences($purchaseHistory, $userReviews);
        
        // Get recommendations based on preferences
        $recommendations = $this->getRecommendationsBasedOnPreferences($preferences);
        
        return response()->json([
            'success' => true,
            'preferences' => $preferences,
            'recommendations' => $recommendations
        ]);
    }

    private function getStockRecommendation($product)
    {
        if ($product->stock <= 0) {
            return 'Sản phẩm hiện tại đã hết hàng. Bạn có thể đặt hàng trước hoặc chọn sản phẩm tương tự.';
        } elseif ($product->stock <= 10) {
            return 'Sản phẩm sắp hết hàng! Hãy đặt hàng ngay để đảm bảo có sản phẩm.';
        } else {
            return 'Sản phẩm còn hàng và sẵn sàng giao đến bạn.';
        }
    }

    private function getSkinTypeRecommendations($skinType)
    {
        $recommendations = [
            'dry' => [
                'focus' => 'Dưỡng ẩm sâu và khóa ẩm',
                'ingredients' => ['Hyaluronic Acid', 'Ceramides', 'Glycerin', 'Shea Butter'],
                'avoid' => ['Alcohol', 'Fragrance', 'Harsh exfoliants']
            ],
            'oily' => [
                'focus' => 'Kiểm soát bã nhờn và làm sạch sâu',
                'ingredients' => ['Salicylic Acid', 'Niacinamide', 'Zinc', 'Tea Tree Oil'],
                'avoid' => ['Heavy oils', 'Thick creams', 'Comedogenic ingredients']
            ],
            'combination' => [
                'focus' => 'Cân bằng và điều chỉnh theo vùng da',
                'ingredients' => ['Hyaluronic Acid', 'Niacinamide', 'Gentle exfoliants'],
                'avoid' => ['Heavy products', 'Harsh ingredients']
            ],
            'sensitive' => [
                'focus' => 'Dịu nhẹ và phục hồi',
                'ingredients' => ['Aloe Vera', 'Centella Asiatica', 'Panthenol', 'Ceramides'],
                'avoid' => ['Fragrance', 'Alcohol', 'Harsh acids', 'Physical exfoliants']
            ],
            'normal' => [
                'focus' => 'Duy trì sự cân bằng tự nhiên',
                'ingredients' => ['Antioxidants', 'Gentle hydrators', 'SPF'],
                'avoid' => ['Over-exfoliation', 'Harsh products']
            ]
        ];
        
        return $recommendations[$skinType] ?? $recommendations['normal'];
    }

    private function getProductsForSkinType($skinType, $concerns = [])
    {
        $query = Product::active()->where('skin_type', $skinType);
        
        if (!empty($concerns)) {
            $concerns = explode(',', $concerns);
            foreach ($concerns as $concern) {
                $concern = trim(strtolower($concern));
                $query->where(function($q) use ($concern) {
                    $q->where('name', 'like', "%{$concern}%")
                      ->orWhere('description', 'like', "%{$concern}%")
                      ->orWhere('ingredients', 'like', "%{$concern}%");
                });
            }
        }
        
        return $query->orderBy('average_rating', 'desc')->limit(5)->get();
    }

    private function getSkincareRoutine($skinType)
    {
        $routines = [
            'dry' => [
                'morning' => ['Sữa rửa mặt dịu nhẹ', 'Toner dưỡng ẩm', 'Serum Hyaluronic Acid', 'Kem dưỡng ẩm sâu', 'Kem chống nắng'],
                'evening' => ['Tẩy trang', 'Sữa rửa mặt', 'Toner', 'Serum dưỡng ẩm', 'Kem dưỡng ban đêm']
            ],
            'oily' => [
                'morning' => ['Sữa rửa mặt gel', 'Toner kiềm dầu', 'Serum Niacinamide', 'Kem dưỡng không gây nhờn', 'Kem chống nắng'],
                'evening' => ['Tẩy trang', 'Sữa rửa mặt', 'Toner', 'Serum trị mụn', 'Kem dưỡng nhẹ']
            ],
            'combination' => [
                'morning' => ['Sữa rửa mặt cân bằng', 'Toner', 'Serum đa năng', 'Kem dưỡng nhẹ', 'Kem chống nắng'],
                'evening' => ['Tẩy trang', 'Sữa rửa mặt', 'Toner', 'Serum', 'Kem dưỡng']
            ],
            'sensitive' => [
                'morning' => ['Sữa rửa mặt dịu nhẹ', 'Toner phục hồi', 'Serum Centella', 'Kem dưỡng dịu nhẹ', 'Kem chống nắng vật lý'],
                'evening' => ['Tẩy trang dầu', 'Sữa rửa mặt', 'Toner', 'Serum phục hồi', 'Kem dưỡng ban đêm']
            ]
        ];
        
        return $routines[$skinType] ?? $routines['combination'];
    }

    private function getSkincareTips($skinType)
    {
        $tips = [
            'dry' => [
                'Uống đủ nước (2-3 lít/ngày)',
                'Sử dụng máy tạo ẩm trong phòng',
                'Tránh tắm nước quá nóng',
                'Thoa kem dưỡng ẩm ngay sau khi rửa mặt'
            ],
            'oily' => [
                'Rửa mặt 2 lần/ngày, không quá 3 lần',
                'Sử dụng giấy thấm dầu thay vì rửa mặt nhiều',
                'Tránh chạm tay lên mặt',
                'Thay vỏ gối thường xuyên'
            ],
            'combination' => [
                'Điều chỉnh sản phẩm theo vùng da',
                'Sử dụng sản phẩm cân bằng',
                'Không bỏ qua kem chống nắng',
                'Theo dõi phản ứng của da'
            ],
            'sensitive' => [
                'Test sản phẩm trước khi sử dụng',
                'Tránh thay đổi routine đột ngột',
                'Sử dụng sản phẩm không hương liệu',
                'Bảo vệ da khỏi tác nhân gây kích ứng'
            ]
        ];
        
        return $tips[$skinType] ?? $tips['combination'];
    }

    private function getPopularCombinations($product)
    {
        // This would typically come from purchase data analysis
        $combinations = [
            'skincare' => [
                'Sữa rửa mặt + Toner + Kem dưỡng ẩm',
                'Serum + Kem dưỡng ẩm + Kem chống nắng',
                'Tẩy trang + Sữa rửa mặt + Mặt nạ'
            ],
            'makeup' => [
                'Kem nền + Phấn phủ + Son môi',
                'Kem lót + Phấn mắt + Mascara',
                'Kem che khuyết điểm + Phấn phủ + Highlight'
            ]
        ];
        
        return $combinations[$product->category] ?? [];
    }

    private function getUsageTips($product)
    {
        $tips = [
            'skincare' => [
                'Thoa một lượng vừa đủ, không quá nhiều',
                'Massage nhẹ nhàng theo chiều từ trong ra ngoài',
                'Đợi sản phẩm thấm hoàn toàn trước khi thoa lớp tiếp theo',
                'Sử dụng đều đặn để thấy hiệu quả tốt nhất'
            ],
            'makeup' => [
                'Luôn dưỡng ẩm trước khi trang điểm',
                'Sử dụng cọ trang điểm sạch',
                'Tẩy trang kỹ trước khi ngủ',
                'Bảo quản sản phẩm ở nơi khô ráo, thoáng mát'
            ]
        ];
        
        return $tips[$product->category] ?? $tips['skincare'];
    }

    private function analyzeIngredients($ingredients)
    {
        if (!$ingredients) {
            return [];
        }
        
        $ingredients = strtolower($ingredients);
        $analysis = [];
        
        // Hydrating ingredients
        if (strpos($ingredients, 'hyaluronic acid') !== false) {
            $analysis[] = ['name' => 'Hyaluronic Acid', 'benefit' => 'Dưỡng ẩm sâu, căng mịn da', 'type' => 'good'];
        }
        if (strpos($ingredients, 'glycerin') !== false) {
            $analysis[] = ['name' => 'Glycerin', 'benefit' => 'Dưỡng ẩm, giữ ẩm', 'type' => 'good'];
        }
        
        // Anti-aging ingredients
        if (strpos($ingredients, 'retinol') !== false) {
            $analysis[] = ['name' => 'Retinol', 'benefit' => 'Chống lão hóa, tái tạo tế bào', 'type' => 'good'];
        }
        if (strpos($ingredients, 'vitamin c') !== false) {
            $analysis[] = ['name' => 'Vitamin C', 'benefit' => 'Chống oxy hóa, làm sáng da', 'type' => 'good'];
        }
        
        // Acne-fighting ingredients
        if (strpos($ingredients, 'salicylic acid') !== false) {
            $analysis[] = ['name' => 'Salicylic Acid', 'benefit' => 'Trị mụn, tẩy tế bào chết', 'type' => 'good'];
        }
        if (strpos($ingredients, 'benzoyl peroxide') !== false) {
            $analysis[] = ['name' => 'Benzoyl Peroxide', 'benefit' => 'Kháng khuẩn, trị mụn', 'type' => 'good'];
        }
        
        // Soothing ingredients
        if (strpos($ingredients, 'aloe vera') !== false) {
            $analysis[] = ['name' => 'Aloe Vera', 'benefit' => 'Dịu nhẹ, phục hồi da', 'type' => 'good'];
        }
        if (strpos($ingredients, 'centella') !== false) {
            $analysis[] = ['name' => 'Centella Asiatica', 'benefit' => 'Phục hồi, làm lành da', 'type' => 'good'];
        }
        
        return $analysis;
    }

    private function analyzeUserPreferences($purchaseHistory, $userReviews)
    {
        $preferences = [
            'favorite_categories' => $purchaseHistory->keys()->toArray(),
            'skin_concerns' => [],
            'price_range' => 'medium',
            'brand_preferences' => []
        ];
        
        // Analyze reviews for skin concerns
        foreach ($userReviews as $review) {
            $comment = strtolower($review->comment);
            if (strpos($comment, 'khô') !== false) {
                $preferences['skin_concerns'][] = 'dry';
            }
            if (strpos($comment, 'dầu') !== false) {
                $preferences['skin_concerns'][] = 'oily';
            }
            if (strpos($comment, 'mụn') !== false) {
                $preferences['skin_concerns'][] = 'acne';
            }
        }
        
        $preferences['skin_concerns'] = array_unique($preferences['skin_concerns']);
        
        return $preferences;
    }

    private function getRecommendationsBasedOnPreferences($preferences)
    {
        $query = Product::active();
        
        // Filter by favorite categories
        if (!empty($preferences['favorite_categories'])) {
            $query->whereIn('category', $preferences['favorite_categories']);
        }
        
        // Filter by skin concerns
        if (!empty($preferences['skin_concerns'])) {
            $skinType = $preferences['skin_concerns'][0]; // Use first concern
            $query->where('skin_type', $skinType);
        }
        
        return $query->orderBy('average_rating', 'desc')
                    ->orderBy('reviews_count', 'desc')
                    ->limit(5)
                    ->get();
    }
}
