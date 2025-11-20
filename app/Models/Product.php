<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        'category',
        'brand',
        'sku',
        'stock',
        'is_featured',
        'is_active',
        'meta_title',
        'meta_description',
        'ingredients',
        'usage_instructions',
        'skin_type',
        'age_group',
        'shelf_life',
        'weight',
        'dimensions',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'stock' => 'integer',
    ];

    // Categories
    const CATEGORIES = [
        'skincare' => 'Chăm sóc da',
        'makeup' => 'Trang điểm',
        'perfume' => 'Nước hoa',
        'haircare' => 'Chăm sóc tóc',
        'bodycare' => 'Chăm sóc cơ thể',
        'tools' => 'Dụng cụ làm đẹp',
    ];

    // Skin Types
    const SKIN_TYPES = [
        'normal' => 'Da thường',
        'dry' => 'Da khô',
        'oily' => 'Da dầu',
        'combination' => 'Da hỗn hợp',
        'sensitive' => 'Da nhạy cảm',
        'acne-prone' => 'Da dễ mụn',
        'mature' => 'Da trưởng thành',
    ];

    // Age Groups
    const AGE_GROUPS = [
        'teen' => '13-19 tuổi',
        'young' => '20-29 tuổi',
        'adult' => '30-39 tuổi',
        'mature' => '40-49 tuổi',
        'senior' => '50+ tuổi',
        'all' => 'Mọi lứa tuổi',
    ];


    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function getReviewsCountAttribute()
    {
        return $this->reviews()->count();
    }

    // AI Recommendation Methods
    public function getAiRecommendations($userPreferences = [])
    {
        $query = self::where('is_active', true);
        
        // Filter by skin type if provided
        if (!empty($userPreferences['skin_type'])) {
            $query->where('skin_type', $userPreferences['skin_type']);
        }
        
        // Filter by age group if provided
        if (!empty($userPreferences['age_group'])) {
            $query->where('age_group', $userPreferences['age_group']);
        }
        
        // Filter by category if provided
        if (!empty($userPreferences['category'])) {
            $query->where('category', $userPreferences['category']);
        }
        
        // Order by rating and reviews count
        $query->orderBy('average_rating', 'desc')
              ->orderBy('reviews_count', 'desc')
              ->limit(5);
        
        return $query->get();
    }

    public function getSimilarProducts()
    {
        return self::where('category', $this->category)
                  ->where('id', '!=', $this->id)
                  ->where('is_active', true)
                  ->orderBy('average_rating', 'desc')
                  ->limit(4)
                  ->get();
    }

    public function getCategoryNameAttribute()
    {
        return self::CATEGORIES[$this->category] ?? 'Khác';
    }

    public function getSkinTypeNameAttribute()
    {
        return self::SKIN_TYPES[$this->skin_type] ?? 'Không xác định';
    }

    public function getAgeGroupNameAttribute()
    {
        return self::AGE_GROUPS[$this->age_group] ?? 'Mọi lứa tuổi';
    }

    public function getFormattedPriceAttribute()
    {
        return number_format($this->price) . ' VND';
    }

    public function getDiscountPriceAttribute()
    {
        // Calculate discount if any
        return $this->price;
    }

    public function getIsInStockAttribute()
    {
        return $this->stock > 0;
    }

    public function getStockStatusAttribute()
    {
        if ($this->stock <= 0) {
            return 'Hết hàng';
        } elseif ($this->stock <= 10) {
            return 'Sắp hết hàng';
        } else {
            return 'Còn hàng';
        }
    }

    public function getImageUrlAttribute()
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return asset('storage/' . $this->image);
        }
        
        // Sử dụng placeholder images dựa trên category
        $placeholders = [
            'skincare' => 'https://via.placeholder.com/400x400/FFB6C1/FFFFFF?text=Skincare',
            'makeup' => 'https://via.placeholder.com/400x400/FF69B4/FFFFFF?text=Makeup',
            'perfume' => 'https://via.placeholder.com/400x400/DDA0DD/FFFFFF?text=Perfume',
            'haircare' => 'https://via.placeholder.com/400x400/98FB98/FFFFFF?text=Haircare',
            'bodycare' => 'https://via.placeholder.com/400x400/F0E68C/FFFFFF?text=Bodycare',
            'tools' => 'https://via.placeholder.com/400x400/87CEEB/FFFFFF?text=Tools',
        ];
        
        return $placeholders[$this->category] ?? 'https://via.placeholder.com/400x400/CCCCCC/FFFFFF?text=Product';
    }

    // Scopes for filtering
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeBySkinType($query, $skinType)
    {
        return $query->where('skin_type', $skinType);
    }

    public function scopeByAgeGroup($query, $ageGroup)
    {
        return $query->where('age_group', $ageGroup);
    }

    public function scopeByPriceRange($query, $min, $max)
    {
        return $query->whereBetween('price', [$min, $max]);
    }

    public function scopeByRating($query, $minRating)
    {
        return $query->whereHas('reviews', function($q) use ($minRating) {
            $q->havingRaw('AVG(rating) >= ?', [$minRating]);
        });
    }

    // AI Analysis Methods
    public function getAiAnalysis()
    {
        $analysis = [
            'suitable_for' => [],
            'benefits' => [],
            'warnings' => [],
            'recommended_usage' => '',
        ];

        // Analyze ingredients for skin type compatibility
        if ($this->ingredients) {
            $ingredients = strtolower($this->ingredients);
            
            // Check for hydrating ingredients
            if (strpos($ingredients, 'hyaluronic acid') !== false || 
                strpos($ingredients, 'glycerin') !== false) {
                $analysis['suitable_for'][] = 'Da khô';
                $analysis['benefits'][] = 'Dưỡng ẩm sâu';
            }
            
            // Check for oil control ingredients
            if (strpos($ingredients, 'salicylic acid') !== false || 
                strpos($ingredients, 'niacinamide') !== false) {
                $analysis['suitable_for'][] = 'Da dầu';
                $analysis['benefits'][] = 'Kiểm soát bã nhờn';
            }
            
            // Check for sensitive skin ingredients
            if (strpos($ingredients, 'aloe vera') !== false || 
                strpos($ingredients, 'centella') !== false) {
                $analysis['suitable_for'][] = 'Da nhạy cảm';
                $analysis['benefits'][] = 'Dịu nhẹ, phục hồi';
            }
            
            // Check for anti-aging ingredients
            if (strpos($ingredients, 'retinol') !== false || 
                strpos($ingredients, 'vitamin c') !== false) {
                $analysis['suitable_for'][] = 'Da trưởng thành';
                $analysis['benefits'][] = 'Chống lão hóa';
            }
        }

        // Set recommended usage based on category
        switch ($this->category) {
            case 'skincare':
                $analysis['recommended_usage'] = 'Sử dụng 1-2 lần/ngày, thoa đều lên da sạch';
                break;
            case 'makeup':
                $analysis['recommended_usage'] = 'Áp dụng sau khi dưỡng ẩm, tẩy trang kỹ trước khi ngủ';
                break;
            case 'perfume':
                $analysis['recommended_usage'] = 'Xịt lên cổ tay, cổ, sau tai. Tránh xịt trực tiếp lên quần áo';
                break;
            default:
                $analysis['recommended_usage'] = 'Sử dụng theo hướng dẫn trên bao bì';
        }

        return $analysis;
    }

    // Search functionality
    public static function search($query)
    {
        return self::where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('brand', 'like', "%{$query}%")
                  ->orWhere('ingredients', 'like', "%{$query}%")
                  ->where('is_active', true);
    }

    // Stock Management Methods
    public function hasEnoughStock($quantity)
    {
        return $this->stock >= $quantity;
    }

    public function getAvailableStock()
    {
        return max(0, $this->stock);
    }

    public function isLowStock($threshold = 5)
    {
        return $this->stock <= $threshold && $this->stock > 0;
    }

    public function isOutOfStock()
    {
        return $this->stock <= 0;
    }

    public function reserveStock($quantity)
    {
        if ($this->hasEnoughStock($quantity)) {
            $this->decrement('stock', $quantity);
            return true;
        }
        return false;
    }

    public function restoreStock($quantity)
    {
        $this->increment('stock', $quantity);
        return true;
    }

    public function getStockWarningMessage()
    {
        if ($this->isOutOfStock()) {
            return "Sản phẩm này hiện đang hết hàng";
        } elseif ($this->isLowStock()) {
            return "Chỉ còn {$this->stock} sản phẩm trong kho";
        }
        return null;
    }
}
