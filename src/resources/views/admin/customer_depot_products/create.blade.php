@extends('layouts.admin')

@section('content')
<div class="p-6 bg-white rounded-lg shadow">
    <h1 class="text-2xl font-semibold mb-4">Add New Rule</h1>
    <form action="{{ route('admin.customer-depot-products.store') }}" method="POST">
        @include('admin.customer_depot_products.form', ['rule' => null])
    </form>
</div>
@endsection
