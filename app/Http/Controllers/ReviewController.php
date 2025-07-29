<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:500'
        ]);

        // Kiểm tra user đã đánh giá sản phẩm này chưa
        $existingReview = Review::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($existingReview) {
            // Cập nhật đánh giá cũ
            $existingReview->update([
                'rating' => $request->rating,
                'comment' => $request->comment
            ]);
            return back()->with('success', 'Cập nhật đánh giá thành công!');
        }

        // Tạo đánh giá mới
        Review::create([
            'user_id' => auth()->id(),
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return back()->with('success', 'Đánh giá sản phẩm thành công!');
    }

    public function my()
    {
        $reviews = Review::where('user_id', auth()->id())
            ->with('product')
            ->latest()
            ->paginate(10);
        return view('reviews.my', compact('reviews'));
    }
}
