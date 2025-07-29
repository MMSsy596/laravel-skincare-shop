@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4"><i class="fas fa-credit-card me-2"></i>Thanh toán đơn hàng</h2>
    <div class="row">
        <div class="col-md-7">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin giao hàng</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('cart.checkout.submit') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="shipping_address" class="form-label">Địa chỉ giao hàng</label>
                            <input type="text" class="form-control" id="shipping_address" name="shipping_address" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>
                        <button type="submit" class="btn btn-success btn-lg w-100">
                            <i class="fas fa-check-circle me-2"></i>Xác nhận đặt hàng
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Đơn hàng của bạn</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group mb-3">
                        @php $total = 0; @endphp
                        @foreach($cart as $id => $item)
                            @php $total += $item['price'] * $item['quantity']; @endphp
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    @if(isset($item['image']) && $item['image'])
                                        <img src="{{ asset('storage/' . $item['image']) }}" style="width:50px; height:50px; object-fit:cover;" class="me-2">
                                    @endif
                                    <div>
                                        <strong>{{ $item['name'] }}</strong><br>
                                        <span class="text-muted">x{{ $item['quantity'] }}</span>
                                    </div>
                                </div>
                                <span>{{ number_format($item['price'] * $item['quantity']) }} VND</span>
                            </li>
                        @endforeach
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                            <strong>Tổng tiền</strong>
                            <strong class="text-primary">{{ number_format($total) }} VND</strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 