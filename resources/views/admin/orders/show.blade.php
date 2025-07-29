@extends('layouts.admin')

@section('page-title', 'Chi tiết đơn hàng')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Chi tiết đơn hàng #{{ $order->id }}</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6>Thông tin khách hàng</h6>
                <p><strong>Tên:</strong> {{ $order->user->name }}</p>
                <p><strong>Email:</strong> {{ $order->user->email }}</p>
                <p><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div class="col-md-6">
                <h6>Thông tin đơn hàng</h6>
                <p><strong>Trạng thái:</strong> 
                    <span class="badge bg-{{ $order->status == 'pending' ? 'warning' : ($order->status == 'completed' ? 'success' : 'secondary') }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </p>
                <p><strong>Tổng tiền:</strong> {{ number_format($order->total) }} VND</p>
            </div>
        </div>
        
        <hr>
        @if($order->status == 'cancelled')
            <div class="alert alert-danger">
                Đơn hàng đã bị hủy bởi 
                @if($order->canceller_id == $order->user_id)
                    <strong>người dùng</strong>
                @elseif($order->canceller)
                    <strong>admin</strong> ({{ $order->canceller->name }})
                @else
                    <strong>hệ thống</strong>
                @endif
            </div>
        @endif
        
        <h6>Chi tiết sản phẩm</h6>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($item->product->image)
                                    <img src="{{ asset('storage/' . $item->product->image) }}" style="width:50px; height:50px; object-fit:cover;" class="me-3">
                                @endif
                                <div>
                                    <strong>{{ $item->product->name }}</strong>
                                </div>
                            </div>
                        </td>
                        <td>{{ number_format($item->price) }} VND</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->price * $item->quantity) }} VND</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
            @if($order->status == 'pending')
                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="d-inline-block ms-2">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="completed">
                    <button type="submit" class="btn btn-success">Đồng ý</button>
                </form>
                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="d-inline-block ms-2">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="cancelled">
                    <button type="submit" class="btn btn-danger">Hủy</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection 