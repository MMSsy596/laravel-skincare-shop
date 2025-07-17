@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Thêm sản phẩm mới</h2>
    <form method="POST" action="{{ route('admin.products.store') }}">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Tên sản phẩm</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Giá</label>
            <input type="number" name="price" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Mô tả</label>
            <textarea name="description" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-success">Lưu</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection
