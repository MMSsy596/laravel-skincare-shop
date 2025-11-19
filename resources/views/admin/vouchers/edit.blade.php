@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Chỉnh sửa voucher: {{ $voucher->code }}
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

                    <form method="POST" action="{{ route('admin.vouchers.update', $voucher->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="code" class="form-label fw-semibold">Mã voucher *</label>
                                <input type="text" class="form-control" id="code" name="code" 
                                       value="{{ old('code', $voucher->code) }}" required>
                                <div class="form-text">Mã voucher phải là duy nhất</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="type" class="form-label fw-semibold">Loại giảm giá *</label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="">Chọn loại</option>
                                    <option value="fixed" {{ old('type', $voucher->type) == 'fixed' ? 'selected' : '' }}>Giảm trực tiếp tiền</option>
                                    <option value="percentage" {{ old('type', $voucher->type) == 'percentage' ? 'selected' : '' }}>Giảm theo phần trăm</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="value" class="form-label fw-semibold">Giá trị giảm *</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="value" name="value" 
                                           value="{{ old('value', $voucher->value) }}" min="0" step="0.01" required>
                                    <span class="input-group-text" id="valueUnit">{{ $voucher->type === 'percentage' ? '%' : 'VNĐ' }}</span>
                                </div>
                                <div class="form-text" id="valueHelp">
                                    {{ $voucher->type === 'percentage' ? 'Nhập phần trăm giảm (ví dụ: 10 = 10%)' : 'Nhập số tiền giảm (VNĐ)' }}
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="min_order" class="form-label fw-semibold">Đơn hàng tối thiểu</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="min_order" name="min_order" 
                                           value="{{ old('min_order', $voucher->min_order) }}" min="0" step="1000">
                                    <span class="input-group-text">VNĐ</span>
                                </div>
                            </div>
                            
                            <div class="col-md-6" id="maxDiscountGroup" style="display: {{ $voucher->type === 'percentage' ? 'block' : 'none' }};">
                                <label for="max_discount" class="form-label fw-semibold">Giảm tối đa</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="max_discount" name="max_discount" 
                                           value="{{ old('max_discount', $voucher->max_discount) }}" min="0" step="1000">
                                    <span class="input-group-text">VNĐ</span>
                                </div>
                                <div class="form-text">Áp dụng cho voucher giảm %</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="usage_limit" class="form-label fw-semibold">Giới hạn sử dụng</label>
                                <input type="number" class="form-control" id="usage_limit" name="usage_limit" 
                                       value="{{ old('usage_limit', $voucher->usage_limit) }}" min="1" placeholder="Không giới hạn">
                                <div class="form-text">Đã sử dụng: {{ $voucher->used_count }} lần</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="valid_from" class="form-label fw-semibold">Ngày bắt đầu *</label>
                                <input type="datetime-local" class="form-control" id="valid_from" name="valid_from" 
                                       value="{{ old('valid_from', $voucher->valid_from->format('Y-m-d\TH:i')) }}" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="valid_until" class="form-label fw-semibold">Ngày kết thúc *</label>
                                <input type="datetime-local" class="form-control" id="valid_until" name="valid_until" 
                                       value="{{ old('valid_until', $voucher->valid_until->format('Y-m-d\TH:i')) }}" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold">Tên voucher</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="{{ old('name', $voucher->name) }}" placeholder="Tên hiển thị cho voucher">
                            </div>
                            
                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">Mô tả</label>
                                <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $voucher->description) }}</textarea>
                            </div>
                            
                            <div class="col-12">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                           value="1" {{ old('is_active', $voucher->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Kích hoạt voucher</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_public" name="is_public" 
                                           value="1" {{ old('is_public', $voucher->is_public) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_public">
                                        <strong>Công khai</strong> - Hiển thị trong danh sách cho người dùng chọn khi thanh toán
                                    </label>
                                    <div class="form-text">Nếu không chọn, voucher chỉ có thể nhập thủ công</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.vouchers.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Cập nhật voucher
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('type').addEventListener('change', function() {
    const type = this.value;
    const valueUnit = document.getElementById('valueUnit');
    const valueHelp = document.getElementById('valueHelp');
    const maxDiscountGroup = document.getElementById('maxDiscountGroup');
    const maxDiscountInput = document.getElementById('max_discount');
    
    if (type === 'percentage') {
        valueUnit.textContent = '%';
        valueHelp.textContent = 'Nhập phần trăm giảm (ví dụ: 10 = 10%)';
        maxDiscountGroup.style.display = 'block';
    } else {
        valueUnit.textContent = 'VNĐ';
        valueHelp.textContent = 'Nhập số tiền giảm (VNĐ)';
        maxDiscountGroup.style.display = 'none';
    }
});
</script>

@endsection

