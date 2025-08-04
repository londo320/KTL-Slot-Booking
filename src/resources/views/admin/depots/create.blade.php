<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl font-semibold">Create Product</h2>
  </x-slot>

  <div class="max-w-xl mx-auto py-6">
    <form method="POST" action="{{ route('admin.products.store') }}" class="space-y-4">
      @csrf

      <div>
        <label class="block font-medium">SKU</label>
        <input type="text" name="sku" class="w-full border p-2" value="{{ old('sku') }}">
        @error('sku')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
      </div>

      <div>
        <label class="block font-medium">Description</label>
        <input type="text" name="description" class="w-full border p-2" value="{{ old('description') }}">
        @error('description')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
      </div>

      <div class="text-right">
        <button class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
      </div>
    </form>
  </div>
</x-app-layout>