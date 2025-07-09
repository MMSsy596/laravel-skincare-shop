<h1>Danh sách sản phẩm</h1>
<a href="{{ route('products.create') }}">Thêm sản phẩm</a>

<ul>
@foreach ($products as $product)
    <li>
        {{ $product->name }} - {{ $product->price }} VND
        <a href="{{ route('products.edit', $product) }}">Sửa</a>
        <form action="{{ route('products.destroy', $product) }}" method="POST" style="display:inline;">
            @csrf @method('DELETE')
            <button type="submit">Xóa</button>
        </form>
    </li>
@endforeach
</ul>
