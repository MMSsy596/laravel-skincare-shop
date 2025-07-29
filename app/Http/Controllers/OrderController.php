<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function history()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with('items.product')
            ->latest()
            ->paginate(10);
        return view('orders.history', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::where('user_id', auth()->id())
            ->with(['items.product'])
            ->findOrFail($id);
        return view('orders.show', compact('order'));
    }

    public function update(Request $request, $id)
    {
        $order = Order::where('user_id', auth()->id())->findOrFail($id);
        
        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Chỉ có thể hủy đơn hàng khi đang chờ xử lý.');
        }
        
        if ($request->status === 'cancelled') {
            try {
                DB::beginTransaction();
                
                // Hoàn trả stock cho tất cả sản phẩm trong đơn hàng
                foreach ($order->items as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->restoreStock($item->quantity);
                    }
                }
                
                // Nếu đã thanh toán, hoàn trả tiền (có thể thêm logic hoàn tiền ở đây)
                if ($order->payment_status === 'paid') {
                    $order->payment_status = 'refunded';
                    $order->payment_notes = ($order->payment_notes ? $order->payment_notes . ' | ' : '') . 'Hoàn tiền do hủy đơn hàng';
                }
                
                // Cập nhật trạng thái đơn hàng
                $order->status = 'cancelled';
                $order->canceller_id = auth()->id();
                $order->save();
                
                DB::commit();
                
                return redirect()->route('orders.history')->with('success', 'Đã hủy đơn hàng thành công. Stock đã được hoàn trả.');
                
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()->with('error', 'Có lỗi xảy ra khi hủy đơn hàng: ' . $e->getMessage());
            }
        }
        
        return redirect()->back();
    }
}
