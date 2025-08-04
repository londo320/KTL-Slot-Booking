@extends('layouts.admin')

@section('content')
<div class="p-6 bg-white rounded-lg shadow">
    <h1 class="text-2xl font-semibold mb-4">Edit Rule</h1>
    <form action="{{ route('admin.customer-depot-products.update', $item) }}" method="POST">
        @method('PUT')
        @include('admin.customer_depot_products.form', ['rule' => $item])
    </form>
</div>
@endsection
