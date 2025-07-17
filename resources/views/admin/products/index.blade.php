@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Quản lý sản phẩm</h2>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary mb-3">+ Thêm sản phẩm</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tên</th>
                <th>Giá</th>
                <th>Mô tả</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($products as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>{{ number_format($product->price, 0, ',', '.') }}₫</td>
                <td>{{ $product->description }}</td>
                <td>
                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-warning">Sửa</a>
                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Xóa sản phẩm này?')" class="btn btn-sm btn-danger">Xóa</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
