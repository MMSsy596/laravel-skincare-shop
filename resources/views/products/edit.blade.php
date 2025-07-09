<h1>Sửa sản phẩm</h1>
<form method="POST" action="{{ route('products.update', $product) }}">
    @csrf @method('PUT')
    <label>Tên sản phẩm: <input type="text" name="name" value="{{ $product->name }}"></label><br>
    <label>Giá: <input type="number" name="price" value="{{ $product->price }}"></label><br>
    <label>Mô tả: <textarea name="description">{{ $product->description }}</textarea></label><br>
    <button type="submit">Cập nhật</button>
</form>
