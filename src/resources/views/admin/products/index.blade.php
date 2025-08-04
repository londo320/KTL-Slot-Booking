  {{-- Admin Nav --}}
  @include('layouts.admin-nav')
@extends('layouts.admin')

@section('content')
<div class="p-6 bg-white rounded-lg shadow">
  <h1 class="text-2xl font-semibold mb-4">Products</h1>
  <a href="{{ route('admin.products.create') }}"
     class="inline-block mb-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
    + New Product
  </a>

  @if(session('success'))
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
      {{ session('success') }}
    </div>
  @endif

  <table class="w-full table-auto border-collapse">
    <thead>
      <tr class="bg-gray-100">
        <th class="px-4 py-2 text-left">SKU</th>
        <th class="px-4 py-2 text-left">Name</th>
        <th class="px-4 py-2 text-right">Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach($products as $product)
      <tr class="border-t">
        <td class="px-4 py-2">{{ $product->sku }}</td>
        <td class="px-4 py-2">{{ $product->name }}</td>
        <td class="px-4 py-2 text-right">
          <a href="{{ route('admin.products.edit', $product) }}"
             class="mr-2 text-blue-600 hover:underline">Edit</a>
          <form action="{{ route('admin.products.destroy', $product) }}"
                method="POST" class="inline">
            @csrf @method('DELETE')
            <button type="submit"
                    class="text-red-600 hover:underline"
                    onclick="return confirm('Delete this product?')">
              Delete
            </button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <div class="mt-4">
    {{ $products->links() }}
  </div>
</div>
@endsection
