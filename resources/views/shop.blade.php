@extends('layouts.app')

@section('content')
<div class="container mt-5">
<h1 style="
    color: aquamarine;
">Danh sách sản phẩm</h1>
    <div class="row">
        @foreach($products as $product)
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5>{{ $product->name }}</h5>
                        <p>{{ $product->description }}</p>
                        <form action="{{ route('cart.add', $product->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary">Thêm vào giỏ</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection 