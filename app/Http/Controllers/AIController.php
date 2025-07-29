<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Support\Facades\DB;

class AIController extends Controller
{
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
        
        $recommendations = $query->orderBy('average_rating', 'desc')
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
        $product = Product::find($productId);
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm'
            ]);
        }
        
        $stockInfo = [
            'product_name' => $product->name,
            'current_stock' => $product->stock,
            'status' => $product->stock_status,
            'is_available' => $product->is_in_stock,
            'recommendation' => $this->getStockRecommendation($product)
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
            ->whereHas('reviews', function($query) {
                $query->where('created_at', '>=', now()->subDays(30));
            })
            ->orderBy('reviews_count', 'desc')
            ->orderBy('average_rating', 'desc')
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
