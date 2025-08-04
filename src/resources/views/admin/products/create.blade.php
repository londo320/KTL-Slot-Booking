@extends('layouts.admin')

@section('content')
<div class="p-6 bg-white rounded-lg shadow">
  <h1 class="text-2xl font-semibold mb-4">Add New Product</h1>
  <form action="{{ route('admin.products.store') }}" method="POST">
    @include('admin.products.form', ['product' => new \App\Models\Product])
  </form>
</div>
@endsection
