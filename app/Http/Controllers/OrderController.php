<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

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
            $order->status = 'cancelled';
            $order->canceller_id = auth()->id();
            $order->save();
            return redirect()->route('orders.history')->with('success', 'Đã hủy đơn hàng thành công.');
        }
        return redirect()->back();
    }
}
