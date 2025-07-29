<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = \App\Models\Product::with('reviews');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Stock filter
        if ($request->filled('stock')) {
            switch ($request->stock) {
                case 'in_stock':
                    $query->where('stock', '>', 10);
                    break;
                case 'low_stock':
                    $query->where('stock', '<=', 10)->where('stock', '>', 0);
                    break;
                case 'out_of_stock':
                    $query->where('stock', '<=', 0);
                    break;
            }
        }

        // Sort by
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $products = $query->paginate(15);

        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.products.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|in:skincare,makeup,perfume,haircare,bodycare,tools',
            'brand' => 'nullable|string|max:255',
            'sku' => 'nullable|string|unique:products,sku',
            'stock' => 'required|integer|min:0',
            'skin_type' => 'nullable|string|in:normal,dry,oily,combination,sensitive,acne-prone,mature',
            'age_group' => 'nullable|string|in:teen,young,adult,mature,senior,all',
            'ingredients' => 'nullable|string',
            'usage_instructions' => 'nullable|string',
            'shelf_life' => 'nullable|string|max:255',
            'weight' => 'nullable|string|max:255',
            'dimensions' => 'nullable|string|max:255',
        ]);

        $data = $request->only([
            'name', 'description', 'price', 'category', 'brand', 'sku', 'stock',
            'skin_type', 'age_group', 'ingredients', 'usage_instructions',
            'shelf_life', 'weight', 'dimensions'
        ]);

        // Set default values
        $data['is_featured'] = $request->has('is_featured');
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $data['image'] = $imagePath;
        }

        \App\Models\Product::create($data);
        return redirect()->route('admin.products.index')->with('success', 'Thêm sản phẩm thành công!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = \App\Models\Product::findOrFail($id);
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = \App\Models\Product::findOrFail($id);
        return view('admin.products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|in:skincare,makeup,perfume,haircare,bodycare,tools',
            'brand' => 'nullable|string|max:255',
            'sku' => 'nullable|string|unique:products,sku,' . $id,
            'stock' => 'required|integer|min:0',
            'skin_type' => 'nullable|string|in:normal,dry,oily,combination,sensitive,acne-prone,mature',
            'age_group' => 'nullable|string|in:teen,young,adult,mature,senior,all',
            'ingredients' => 'nullable|string',
            'usage_instructions' => 'nullable|string',
            'shelf_life' => 'nullable|string|max:255',
            'weight' => 'nullable|string|max:255',
            'dimensions' => 'nullable|string|max:255',
        ]);

        $product = \App\Models\Product::findOrFail($id);
        $data = $request->only([
            'name', 'description', 'price', 'category', 'brand', 'sku', 'stock',
            'skin_type', 'age_group', 'ingredients', 'usage_instructions',
            'shelf_life', 'weight', 'dimensions'
        ]);

        // Set boolean values
        $data['is_featured'] = $request->has('is_featured');
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $data['image'] = $imagePath;
        }

        $product->update($data);
        return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = \App\Models\Product::findOrFail($id);
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Xóa sản phẩm thành công!');
    }
}
