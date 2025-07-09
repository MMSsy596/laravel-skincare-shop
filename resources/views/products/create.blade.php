<h1>Thêm sản phẩm</h1>
<form method="POST" action="{{ route('products.store') }}">
    @csrf
    <label>Tên sản phẩm: <input type="text" name="name"></label><br>
    <label>Giá: <input type="number" name="price" step="0.01"></label><br>
    <label>Mô tả: <textarea name="description"></textarea></label><br>
    <button type="submit">Lưu</button>
</form>
