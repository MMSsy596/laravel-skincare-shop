<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('reviews')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->active();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('ingredients', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Skin type filter
        if ($request->filled('skin_type')) {
            $query->where('skin_type', $request->skin_type);
        }

        // Age group filter
        if ($request->filled('age_group')) {
            $query->where('age_group', $request->age_group);
        }

        // Price range filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Stock filter
        if ($request->filled('in_stock') && $request->in_stock) {
            $query->inStock();
        }

        // Featured filter
        if ($request->filled('featured') && $request->featured) {
            $query->featured();
        }

        // Sorting
        switch ($request->sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'rating':
                $query->orderBy('reviews_avg_rating', 'desc');
                break;
            case 'newest':
                $query->latest();
                break;
            case 'popular':
                $query->orderBy('reviews_count', 'desc');
                break;
            default:
                $query->latest();
        }

        $products = $query->paginate(12)->withQueryString();

        // Get categories for filter
        $categories = Product::CATEGORIES;
        $skinTypes = Product::SKIN_TYPES;
        $ageGroups = Product::AGE_GROUPS;

        return view('shop', compact('products', 'categories', 'skinTypes', 'ageGroups'));
    }

    public function show($id)
    {
        $product = Product::with('reviews.user')->findOrFail($id);
        
        // Get AI analysis
        $aiAnalysis = $product->getAiAnalysis();
        
        // Get similar products
        $similarProducts = $product->getSimilarProducts();
        
        // Get AI recommendations based on product category
        $aiRecommendations = Product::active()
            ->where('category', $product->category)
            ->where('id', '!=', $product->id)
            ->withAvg('reviews', 'rating')
            ->orderBy('reviews_avg_rating', 'desc')
            ->limit(4)
            ->get();
        
        return view('products.show', compact('product', 'aiAnalysis', 'similarProducts', 'aiRecommendations'));
    }

    public function getAiRecommendations(Request $request)
    {
        $userPreferences = $request->only(['skin_type', 'age_group', 'category', 'concerns']);
        
        $recommendations = Product::active()
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->when($userPreferences['skin_type'] ?? false, function($query, $skinType) {
                return $query->where('skin_type', $skinType);
            })
            ->when($userPreferences['age_group'] ?? false, function($query, $ageGroup) {
                return $query->where('age_group', $ageGroup);
            })
            ->when($userPreferences['category'] ?? false, function($query, $category) {
                return $query->where('category', $category);
            })
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

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (empty($query)) {
            return redirect()->route('shop');
        }

        $products = Product::search($query)
            ->with('reviews')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->paginate(12)
            ->withQueryString();

        $categories = Product::CATEGORIES;
        $skinTypes = Product::SKIN_TYPES;
        $ageGroups = Product::AGE_GROUPS;

        return view('shop', compact('products', 'categories', 'skinTypes', 'ageGroups', 'query'));
    }

    public function getCategories()
    {
        $categories = Product::CATEGORIES;
        $categoryStats = [];

        foreach ($categories as $key => $name) {
            $categoryStats[$key] = [
                'name' => $name,
                'count' => Product::where('category', $key)->active()->count(),
                'icon' => $this->getCategoryIcon($key)
            ];
        }

        return response()->json($categoryStats);
    }

    private function getCategoryIcon($category)
    {
        $icons = [
            'skincare' => 'fas fa-spa',
            'makeup' => 'fas fa-palette',
            'perfume' => 'fas fa-spray-can',
            'haircare' => 'fas fa-cut',
            'bodycare' => 'fas fa-hand-sparkles',
            'tools' => 'fas fa-tools'
        ];

        return $icons[$category] ?? 'fas fa-box';
    }
}
