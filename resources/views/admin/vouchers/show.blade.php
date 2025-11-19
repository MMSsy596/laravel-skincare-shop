@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-ticket-alt me-2"></i>Chi tiết voucher: {{ $voucher->code }}
                    </h4>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mã voucher</label>
                            <div class="form-control bg-light">
                                <strong class="text-primary">{{ $voucher->code }}</strong>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Loại giảm giá</label>
                            <div class="form-control bg-light">
                                @if($voucher->type === 'fixed')
                                    <span class="badge bg-info">Giảm trực tiếp tiền</span>
                                @else
                                    <span class="badge bg-warning">Giảm theo phần trăm</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Giá trị giảm</label>
                            <div class="form-control bg-light">
                                <strong>
                                    @if($voucher->type === 'fixed')
                                        {{ number_format($voucher->value) }} VNĐ
                                    @else
                                        {{ $voucher->value }}%
                                    @endif
                                </strong>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Đơn hàng tối thiểu</label>
                            <div class="form-control bg-light">
                                {{ number_format($voucher->min_order) }} VNĐ
                            </div>
                        </div>
                        
                        @if($voucher->max_discount)
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Giảm tối đa</label>
                            <div class="form-control bg-light">
                                {{ number_format($voucher->max_discount) }} VNĐ
                            </div>
                        </div>
                        @endif
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Sử dụng</label>
                            <div class="form-control bg-light">
                                @if($voucher->usage_limit)
                                    {{ $voucher->used_count }} / {{ $voucher->usage_limit }} lần
                                @else
                                    {{ $voucher->used_count }} / Không giới hạn
                                @endif
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ngày bắt đầu</label>
                            <div class="form-control bg-light">
                                {{ $voucher->valid_from->format('d/m/Y H:i') }}
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ngày kết thúc</label>
                            <div class="form-control bg-light">
                                {{ $voucher->valid_to->format('d/m/Y H:i') }}
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Trạng thái</label>
                            <div class="form-control bg-light">
                                @if($voucher->is_active)
                                    <span class="badge bg-success">Hoạt động</span>
                                @else
                                    <span class="badge bg-secondary">Không hoạt động</span>
                                @endif
                            </div>
                        </div>
                        
                        @if($voucher->description)
                        <div class="col-12">
                            <label class="form-label fw-semibold">Mô tả</label>
                            <div class="form-control bg-light">
                                {{ $voucher->description }}
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('admin.vouchers.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </a>
                        <div>
                            <a href="{{ route('admin.vouchers.edit', $voucher->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-2"></i>Chỉnh sửa
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

