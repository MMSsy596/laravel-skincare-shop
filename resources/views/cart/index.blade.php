@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4 text-white">Giỏ hàng của bạn</h2>
    @if(count($cart) > 0)
        <ul class="list-group">
            @foreach ($cart as $id => $item)
                <li class="list-group-item d-flex justify-content-between align-items-center bg-dark text-white">
                    <div>
                        <strong>{{ $item['name'] }}</strong> - {{ number_format($item['price']) }} VND
                        <form method="POST" action="{{ route('cart.update', $id) }}" class="d-inline ms-3">
                            @csrf
                            <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" style="color: black; style="width:60px;">
                            <button type="submit" class="btn btn-sm btn-info">Cập nhật</button>
                        </form>
                    </div>
                    <form method="POST" action="{{ route('cart.remove', $id) }}" class="mb-0">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                    </form>
                </li>
            @endforeach
        </ul>
    @else
        <div class="alert alert-info mt-3">Giỏ hàng của bạn đang trống.</div>
    @endif
</div>
@endsection
