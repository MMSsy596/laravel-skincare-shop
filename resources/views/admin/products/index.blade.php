@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="fas fa-boxes me-2"></i>Quản lý sản phẩm
            </h2>
            <p class="text-muted mb-0">Quản lý tất cả sản phẩm mỹ phẩm trong hệ thống</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Thêm sản phẩm mới
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-box text-primary fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h4 class="mb-1">{{ $products->count() }}</h4>
                            <p class="text-muted mb-0">Tổng sản phẩm</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-check-circle text-success fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h4 class="mb-1">{{ $products->where('is_active', true)->count() }}</h4>
                            <p class="text-muted mb-0">Đang hoạt động</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-star text-warning fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h4 class="mb-1">{{ $products->where('is_featured', true)->count() }}</h4>
                            <p class="text-muted mb-0">Sản phẩm nổi bật</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-exclamation-triangle text-danger fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h4 class="mb-1">{{ $products->where('stock', '<=', 10)->where('stock', '>', 0)->count() }}</h4>
                            <p class="text-muted mb-0">Sắp hết hàng</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.products.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Tìm kiếm</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Tên sản phẩm, SKU...">
                </div>
                <div class="col-md-2">
                    <label for="category" class="form-label">Danh mục</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">Tất cả</option>
                        @foreach(\App\Models\Product::CATEGORIES as $key => $name)
                            <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tất cả</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="stock" class="form-label">Tình trạng kho</label>
                    <select class="form-select" id="stock" name="stock">
                        <option value="">Tất cả</option>
                        <option value="in_stock" {{ request('stock') == 'in_stock' ? 'selected' : '' }}>Còn hàng</option>
                        <option value="low_stock" {{ request('stock') == 'low_stock' ? 'selected' : '' }}>Sắp hết</option>
                        <option value="out_of_stock" {{ request('stock') == 'out_of_stock' ? 'selected' : '' }}>Hết hàng</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-2"></i>Tìm kiếm
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-refresh me-2"></i>Làm mới
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Danh sách sản phẩm ({{ $products->count() }})</h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary btn-sm" onclick="exportProducts()">
                        <i class="fas fa-download me-2"></i>Xuất Excel
                    </button>
                    <button class="btn btn-outline-success btn-sm" onclick="bulkActivate()">
                        <i class="fas fa-check me-2"></i>Kích hoạt hàng loạt
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th width="80">Hình ảnh</th>
                            <th>Sản phẩm</th>
                            <th>Danh mục</th>
                            <th>Giá</th>
                            <th>Tồn kho</th>
                            <th>Trạng thái</th>
                            <th>Đánh giá</th>
                            <th width="150">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input product-checkbox" 
                                           value="{{ $product->id }}">
                                </td>
                                <td>
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" 
                                         class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                </td>
                                <td>
                                    <div>
                                        <h6 class="mb-1">{{ $product->name }}</h6>
                                        <small class="text-muted">{{ $product->brand }}</small>
                                        @if($product->sku)
                                            <br><small class="text-muted">SKU: {{ $product->sku }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $product->category_name }}</span>
                                    @if($product->skin_type)
                                        <br><small class="text-muted">{{ $product->skin_type_name }}</small>
                                    @endif
                                </td>
                                <td>
                                    <strong class="text-primary">{{ number_format($product->price) }} VNĐ</strong>
                                </td>
                                <td>
                                    @if($product->stock > 10)
                                        <span class="text-success">{{ $product->stock }}</span>
                                    @elseif($product->stock > 0)
                                        <span class="text-warning">{{ $product->stock }}</span>
                                    @else
                                        <span class="text-danger">0</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        @if($product->is_active)
                                            <span class="badge bg-success">Hoạt động</span>
                                        @else
                                            <span class="badge bg-secondary">Không hoạt động</span>
                                        @endif
                                        @if($product->is_featured)
                                            <span class="badge bg-warning">Nổi bật</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="stars me-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $product->average_rating ? 'text-warning' : 'text-muted' }}" style="font-size: 12px;"></i>
                                            @endfor
                                        </div>
                                        <small class="text-muted">({{ $product->reviews_count }})</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('product.show', $product->id) }}" 
                                           class="btn btn-outline-primary btn-sm" title="Xem">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product->id) }}" 
                                           class="btn btn-outline-warning btn-sm" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger btn-sm" 
                                                onclick="deleteProduct({{ $product->id }})" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-box fa-3x mb-3"></i>
                                        <p>Không có sản phẩm nào</p>
                                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>Thêm sản phẩm đầu tiên
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if($products->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $products->links() }}
        </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa sản phẩm này? Hành động này không thể hoàn tác.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Select all functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Delete product
function deleteProduct(productId) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const form = document.getElementById('deleteForm');
    form.action = `/admin/products/${productId}`;
    modal.show();
}

// Export products
function exportProducts() {
    const searchParams = new URLSearchParams(window.location.search);
    window.location.href = `/admin/products/export?${searchParams.toString()}`;
}

// Bulk activate
function bulkActivate() {
    const selectedProducts = Array.from(document.querySelectorAll('.product-checkbox:checked'))
        .map(checkbox => checkbox.value);
    
    if (selectedProducts.length === 0) {
        alert('Vui lòng chọn ít nhất một sản phẩm');
        return;
    }
    
    if (confirm(`Bạn có chắc chắn muốn kích hoạt ${selectedProducts.length} sản phẩm?`)) {
        // Implement bulk activate functionality
        console.log('Bulk activate:', selectedProducts);
    }
}

// Auto-submit form on filter change
document.querySelectorAll('#category, #status, #stock').forEach(select => {
    select.addEventListener('change', function() {
        this.closest('form').submit();
    });
});
</script>

@endsection
