@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="fas fa-ticket-alt me-2"></i>Quản lý Voucher
            </h2>
            <p class="text-muted mb-0">Quản lý tất cả mã giảm giá trong hệ thống</p>
        </div>
        <a href="{{ route('admin.vouchers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tạo voucher mới
        </a>
    </div>

    <!-- Filters and Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.vouchers.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Tìm kiếm</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Mã voucher, mô tả...">
                </div>
                <div class="col-md-3">
                    <label for="type" class="form-label">Loại</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">Tất cả</option>
                        <option value="fixed" {{ request('type') == 'fixed' ? 'selected' : '' }}>Giảm tiền</option>
                        <option value="percentage" {{ request('type') == 'percentage' ? 'selected' : '' }}>Giảm %</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tất cả</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-2"></i>Tìm kiếm
                    </button>
                    <a href="{{ route('admin.vouchers.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-refresh"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Vouchers Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">Danh sách voucher ({{ $vouchers->total() }})</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Mã voucher</th>
                            <th>Loại</th>
                            <th>Giá trị</th>
                            <th>Đơn tối thiểu</th>
                            <th>Giảm tối đa</th>
                            <th>Sử dụng</th>
                            <th>Thời gian</th>
                            <th>Trạng thái</th>
                            <th width="150">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vouchers as $voucher)
                            <tr>
                                <td>
                                    <strong class="text-primary">{{ $voucher->code }}</strong>
                                    @if($voucher->description)
                                        <br><small class="text-muted">{{ Str::limit($voucher->description, 30) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($voucher->type === 'fixed')
                                        <span class="badge bg-info">Giảm tiền</span>
                                    @else
                                        <span class="badge bg-warning">Giảm %</span>
                                    @endif
                                </td>
                                <td>
                                    @if($voucher->type === 'fixed')
                                        <strong>{{ number_format($voucher->value) }} VNĐ</strong>
                                    @else
                                        <strong>{{ $voucher->value }}%</strong>
                                    @endif
                                </td>
                                <td>{{ number_format($voucher->min_order) }} VNĐ</td>
                                <td>
                                    @if($voucher->max_discount)
                                        {{ number_format($voucher->max_discount) }} VNĐ
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($voucher->usage_limit)
                                        {{ $voucher->used_count }} / {{ $voucher->usage_limit }}
                                    @else
                                        {{ $voucher->used_count }} / <span class="text-muted">∞</span>
                                    @endif
                                </td>
                                <td>
                                    <small>
                                        <div>{{ \Carbon\Carbon::parse($voucher->valid_from)->format('d/m/Y') }}</div>
                                        <div class="text-muted">{{ \Carbon\Carbon::parse($voucher->valid_to)->format('d/m/Y') }}</div>
                                    </small>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        @if($voucher->is_active)
                                            <span class="badge bg-success">Hoạt động</span>
                                        @else
                                            <span class="badge bg-secondary">Không hoạt động</span>
                                        @endif
                                        @if($voucher->is_public)
                                            <span class="badge bg-info">Công khai</span>
                                        @else
                                            <span class="badge bg-dark">Riêng tư</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.vouchers.show', $voucher->id) }}" 
                                           class="btn btn-outline-primary btn-sm" title="Xem">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.vouchers.edit', $voucher->id) }}" 
                                           class="btn btn-outline-warning btn-sm" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger btn-sm" 
                                                onclick="deleteVoucher({{ $voucher->id }})" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-ticket-alt fa-3x mb-3"></i>
                                        <p>Không có voucher nào</p>
                                        <a href="{{ route('admin.vouchers.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>Tạo voucher đầu tiên
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
    @if($vouchers->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $vouchers->links() }}
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
                <p>Bạn có chắc chắn muốn xóa voucher này? Hành động này không thể hoàn tác.</p>
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
function deleteVoucher(voucherId) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const form = document.getElementById('deleteForm');
    form.action = `/admin/vouchers/${voucherId}`;
    modal.show();
}
</script>

@endsection

