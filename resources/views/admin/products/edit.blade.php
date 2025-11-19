@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm admin-form-card">
                <div class="card-header admin-card-header-warning">
                    <h4 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Chỉnh sửa sản phẩm: {{ $product->name }}
                    </h4>
                </div>
                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-8">
                                <h5 class="fw-bold mb-3 text-primary">
                                    <i class="fas fa-info-circle me-2"></i>Thông tin cơ bản
                                </h5>
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label fw-semibold">Tên sản phẩm *</label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="{{ old('name', $product->name) }}" required>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="brand" class="form-label fw-semibold">Thương hiệu</label>
                                        <input type="text" class="form-control" id="brand" name="brand" 
                                               value="{{ old('brand', $product->brand) }}">
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="sku" class="form-label fw-semibold">Mã SKU</label>
                                        <input type="text" class="form-control" id="sku" name="sku" 
                                               value="{{ old('sku', $product->sku) }}">
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="price" class="form-label fw-semibold">Giá (VND) *</label>
                                        <input type="number" class="form-control" id="price" name="price" 
                                               value="{{ old('price', $product->price) }}" min="0" step="1000" required>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="stock" class="form-label fw-semibold">Số lượng tồn kho *</label>
                                        <input type="number" class="form-control" id="stock" name="stock" 
                                               value="{{ old('stock', $product->stock) }}" min="0" required>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="category" class="form-label fw-semibold">Danh mục *</label>
                                        <select class="form-select" id="category" name="category" required>
                                            <option value="">Chọn danh mục</option>
                                            @foreach(\App\Models\Product::CATEGORIES as $key => $name)
                                                <option value="{{ $key }}" {{ old('category', $product->category) == $key ? 'selected' : '' }}>
                                                    {{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="mt-3">
                                    <label for="description" class="form-label fw-semibold">Mô tả sản phẩm</label>
                                    <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
                                </div>
                            </div>
                            
                            <!-- Product Image -->
                            <div class="col-md-4">
                                <h5 class="fw-bold mb-3 text-primary">
                                    <i class="fas fa-image me-2"></i>Hình ảnh sản phẩm
                                </h5>
                                
                                <div class="mb-3">
                                    <label for="image" class="form-label fw-semibold">Chọn hình ảnh mới</label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                    <div class="form-text">Để trống nếu không muốn thay đổi ảnh</div>
                                </div>
                                
                                <div class="current-image mb-3">
                                    <label class="form-label fw-semibold">Ảnh hiện tại:</label>
                                    @if($product->image)
                                        <img src="{{ $product->image_url }}" alt="Current Image" 
                                             class="img-fluid rounded border" style="max-height: 200px;">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                             style="height: 200px;">
                                            <i class="fas fa-image text-muted fa-3x"></i>
                                        </div>
                                    @endif
                                </div>
                                
                                <div id="imagePreview" class="d-none">
                                    <label class="form-label fw-semibold">Ảnh mới:</label>
                                    <img src="" alt="Preview" class="img-fluid rounded border" style="max-height: 200px;">
                                </div>
                                
                                <!-- Status Toggles -->
                                <div class="mt-4">
                                    <h6 class="fw-bold mb-2">Trạng thái</h6>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                               {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Kích hoạt sản phẩm</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" 
                                               {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_featured">Sản phẩm nổi bật</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <!-- Beauty Specific Information -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="fw-bold mb-3 text-primary">
                                    <i class="fas fa-spa me-2"></i>Thông tin chuyên môn
                                </h5>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="skin_type" class="form-label fw-semibold">Phù hợp với loại da</label>
                                <select class="form-select" id="skin_type" name="skin_type">
                                    <option value="">Chọn loại da</option>
                                    @foreach(\App\Models\Product::SKIN_TYPES as $key => $name)
                                        <option value="{{ $key }}" {{ old('skin_type', $product->skin_type) == $key ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="age_group" class="form-label fw-semibold">Độ tuổi phù hợp</label>
                                <select class="form-select" id="age_group" name="age_group">
                                    @foreach(\App\Models\Product::AGE_GROUPS as $key => $name)
                                        <option value="{{ $key }}" {{ old('age_group', $product->age_group) == $key ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-12 mt-3">
                                <label for="ingredients" class="form-label fw-semibold">Thành phần chính</label>
                                <textarea class="form-control" id="ingredients" name="ingredients" rows="3" 
                                          placeholder="Liệt kê các thành phần chính của sản phẩm...">{{ old('ingredients', $product->ingredients) }}</textarea>
                                <div class="form-text">Ví dụ: Hyaluronic Acid, Vitamin C, Retinol, Niacinamide...</div>
                            </div>
                            
                            <div class="col-12 mt-3">
                                <label for="usage_instructions" class="form-label fw-semibold">Hướng dẫn sử dụng</label>
                                <textarea class="form-control" id="usage_instructions" name="usage_instructions" rows="3" 
                                          placeholder="Hướng dẫn cách sử dụng sản phẩm...">{{ old('usage_instructions', $product->usage_instructions) }}</textarea>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <!-- Product Specifications -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="fw-bold mb-3 text-primary">
                                    <i class="fas fa-cogs me-2"></i>Thông số kỹ thuật
                                </h5>
                            </div>
                            
                            <div class="col-md-4">
                                <label for="weight" class="form-label fw-semibold">Trọng lượng</label>
                                <input type="text" class="form-control" id="weight" name="weight" 
                                       value="{{ old('weight', $product->weight) }}" placeholder="Ví dụ: 30ml, 50g">
                            </div>
                            
                            <div class="col-md-4">
                                <label for="dimensions" class="form-label fw-semibold">Kích thước</label>
                                <input type="text" class="form-control" id="dimensions" name="dimensions" 
                                       value="{{ old('dimensions', $product->dimensions) }}" placeholder="Ví dụ: 5x5x10cm">
                            </div>
                            
                            <div class="col-md-4">
                                <label for="shelf_life" class="form-label fw-semibold">Hạn sử dụng</label>
                                <input type="text" class="form-control" id="shelf_life" name="shelf_life" 
                                       value="{{ old('shelf_life', $product->shelf_life) }}" placeholder="Ví dụ: 24 tháng">
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Quay lại
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-2"></i>Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Image preview functionality
document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('imagePreview');
    const img = preview.querySelector('img');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            preview.classList.remove('d-none');
        }
        reader.readAsDataURL(file);
    } else {
        preview.classList.add('d-none');
    }
});

// Auto-generate SKU
document.getElementById('name').addEventListener('input', function() {
    const name = this.value;
    const skuField = document.getElementById('sku');
    
    if (name && !skuField.value) {
        const sku = name.toUpperCase()
            .replace(/[^A-Z0-9]/g, '')
            .substring(0, 8);
        skuField.value = sku + '-' + Math.random().toString(36).substr(2, 4).toUpperCase();
    }
});

// Category change handler
document.getElementById('category').addEventListener('change', function() {
    const category = this.value;
    const skinTypeField = document.getElementById('skin_type');
    
    // Reset skin type for non-skincare categories
    if (category !== 'skincare') {
        skinTypeField.value = '';
    }
});
</script>

@endsection
