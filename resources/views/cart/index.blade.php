@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4 text-white">Giỏ hàng của bạn</h2>
    @if(count($cart) > 0)
        <ul class="list-group mb-4">
            @php $total = 0; @endphp
            @foreach ($cart as $id => $item)
                @php $total += $item['price'] * $item['quantity']; @endphp
                <li class="list-group-item d-flex justify-content-between align-items-center bg-dark text-white">
                    <div>
                        <strong>{{ $item['name'] }}</strong> - {{ number_format($item['price']) }} VND - Số lượng: {{ $item['quantity'] }}
                        <form method="POST" action="{{ route('cart.update', $id) }}" class="d-inline ms-3">
                            @csrf
                            <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" style="width:60px;" class="form-control form-control-sm bg-light text-dark d-inline-block" />
                            <button type="submit" class="btn btn-sm btn-info">Cập nhật</button>
                        </form>
                    </div>
                    <form method="POST" action="{{ route('cart.remove', $id) }}" class="mb-0">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                    </form>
                </li>
            @endforeach
            <li class="list-group-item d-flex justify-content-between align-items-center bg-light text-dark">
                <strong>Tổng tiền</strong>
                <strong class="text-primary">{{ number_format($total) }} VND</strong>
            </li>
        </ul>
        <div class="d-flex justify-content-end">
            <a href="{{ route('cart.checkout') }}" class="btn btn-success btn-lg">
                <i class="fas fa-credit-card me-2"></i>Thanh toán
            </a>
        </div>
    @else
        <div class="alert alert-info mt-3">Giỏ hàng của bạn đang trống.</div>
    @endif
</div>
@endsection
