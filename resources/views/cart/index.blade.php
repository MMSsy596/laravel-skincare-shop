@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-shopping-cart me-2"></i>Giỏ hàng của bạn
                    </h4>
                </div>
                <div class="card-body">
                    @if(count($cart) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Giá</th>
                                        <th>Số lượng</th>
                                        <th>Tổng</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $total = 0; $hasStockIssues = false; @endphp
                                    @foreach ($cart as $id => $item)
                                        @php 
                                            $subtotal = $item['price'] * $item['quantity'];
                                            $total += $subtotal;
                                            $stock = $item['stock'] ?? 0;
                                            $isOutOfStock = $stock < $item['quantity'];
                                            if ($isOutOfStock) $hasStockIssues = true;
                                        @endphp
                                        <tr class="{{ $isOutOfStock ? 'table-warning' : '' }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $item['image'] ?? 'https://via.placeholder.com/80x80/CCCCCC/FFFFFF?text=Product' }}" 
                                                         alt="{{ $item['name'] }}" 
                                                         class="rounded me-3" 
                                                         style="width: 80px; height: 80px; object-fit: cover;">
                                                    <div>
                                                        <h6 class="mb-1">{{ $item['name'] }}</h6>
                                                        <small class="text-muted">SKU: {{ \App\Models\Product::find($id)->sku ?? 'N/A' }}</small>
                                                        @if($isOutOfStock)
                                                            <div class="mt-1">
                                                                <span class="badge bg-warning text-dark">
                                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                                    Chỉ còn {{ $stock }} sản phẩm
                                                                </span>
                                                            </div>
                                                        @elseif($stock <= 5)
                                                            <div class="mt-1">
                                                                <span class="badge bg-info">
                                                                    <i class="fas fa-info-circle me-1"></i>
                                                                    Còn {{ $stock }} sản phẩm
                                                                </span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <strong class="text-primary">{{ number_format($item['price']) }} VNĐ</strong>
                                            </td>
                                            <td>
                                                <form method="POST" action="{{ route('cart.update', $id) }}" class="d-flex align-items-center">
                                                    @csrf
                                                    <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="{{ $stock }}"
                                                           style="width: 80px;" class="form-control me-2" />
                                                    <button type="submit" class="btn btn-outline-primary btn-sm">
                                                        <i class="fas fa-sync-alt"></i>
                                                    </button>
                                                </form>
                                                @if($isOutOfStock)
                                                    <small class="text-danger d-block mt-1">
                                                        <i class="fas fa-exclamation-circle me-1"></i>
                                                        Vượt quá số lượng có sẵn
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ number_format($subtotal) }} VNĐ</strong>
                                            </td>
                                            <td>
                                                <form method="POST" action="{{ route('cart.remove', $id) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                            onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="3" class="text-end">
                                            <h5 class="mb-0">Tổng tiền:</h5>
                                        </td>
                                        <td>
                                            <h5 class="mb-0 text-primary">{{ number_format($total) }} VNĐ</h5>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        @if($hasStockIssues)
                            <div class="alert alert-warning mt-3">
                                <h6 class="alert-heading">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Cảnh báo về kho hàng
                                </h6>
                                <p class="mb-0">
                                    Một số sản phẩm trong giỏ hàng của bạn vượt quá số lượng có sẵn trong kho. 
                                    Vui lòng điều chỉnh số lượng hoặc xóa sản phẩm để tiếp tục thanh toán.
                                </p>
                            </div>
                        @endif
                        
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('shop') }}" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-2"></i>Tiếp tục mua hàng
                            </a>
                            @if($hasStockIssues)
                                <button class="btn btn-secondary btn-lg" disabled>
                                    <i class="fas fa-exclamation-triangle me-2"></i>Không thể thanh toán
                                </button>
                            @else
                                <a href="{{ route('cart.checkout') }}" class="btn btn-success btn-lg">
                                    <i class="fas fa-credit-card me-2"></i>Thanh toán
                                </a>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Giỏ hàng của bạn đang trống</h5>
                            <p class="text-muted">Hãy thêm sản phẩm vào giỏ hàng để bắt đầu mua sắm!</p>
                            <a href="{{ route('shop') }}" class="btn btn-primary">
                                <i class="fas fa-shopping-bag me-2"></i>Mua sắm ngay
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
