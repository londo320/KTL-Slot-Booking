<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl font-semibold">➕ Add New Product</h2>
  </x-slot>

  <div class="max-w-xl mx-auto py-6">
    @if($errors->any())
      <div class="bg-red-100 text-red-700 p-3 mb-4 rounded">
        <ul class="list-disc list-inside">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('admin.products.store') }}" method="POST" class="space-y-4">
      @csrf

      <div>
        <label for="sku" class="block text-sm font-medium">SKU</label>
        <input type="text" name="sku" id="sku" value="{{ old('sku') }}" required
               class="w-full border rounded px-3 py-2">
      </div>

      <div>
        <label for="description" class="block text-sm font-medium">Description</label>
        <input type="text" name="description" id="description" value="{{ old('description') }}" required
               class="w-full border rounded px-3 py-2">
      </div>

      <div class="flex justify-end">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
          💾 Create Product
        </button>
      </div>
    </form>
  </div>
</x-app-layout>
