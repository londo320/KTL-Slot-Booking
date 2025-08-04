@extends('layouts.admin')

@section('content')
<div class="p-6 bg-white rounded-lg shadow">
  <h1 class="text-2xl font-semibold mb-4">Edit Product</h1>
  <form action="{{ route('admin.products.update', $product) }}" method="POST">
    @method('PUT')
    @include('admin.products.form', ['product' => $product])
  </form>
</div>
@endsection
