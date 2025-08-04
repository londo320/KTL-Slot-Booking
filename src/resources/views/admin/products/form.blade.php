@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
  {{-- SKU --}}
  <div>
    <label for="sku" class="block text-sm font-medium">SKU</label>
    <input type="text" name="sku" id="sku"
           value="{{ old('sku', $product->sku ?? '') }}"
           class="mt-1 block w-full border rounded p-2" />
    @error('sku') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
  </div>

  {{-- Description --}}
  <div>
    <label for="description" class="block text-sm font-medium">Description</label>
    <textarea name="description" id="description" rows="3"
              class="mt-1 block w-full border rounded p-2">{{ old('description', $product->description ?? '') }}</textarea>
    @error('description') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
  </div>

  {{-- Default Case Count --}}
  <div>
    <label for="default_case_count" class="block text-sm font-medium">Default Case Count</label>
    <input type="number" name="default_case_count" id="default_case_count"
           value="{{ old('default_case_count', $product->default_case_count ?? '') }}"
           class="mt-1 block w-full border rounded p-2" />
    @error('default_case_count') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
  </div>

  {{-- Default Pallets --}}
  <div>
    <label for="default_pallets" class="block text-sm font-medium">Default Pallets</label>
    <input type="number" name="default_pallets" id="default_pallets"
           value="{{ old('default_pallets', $product->default_pallets ?? '') }}"
           class="mt-1 block w-full border rounded p-2" />
    @error('default_pallets') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
  </div>
</div>

<div class="mt-6">
  <button type="submit"
          class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700">
    Save Product
  </button>
  <a href="{{ route('admin.products.index') }}"
     class="ml-4 text-gray-600 hover:underline">Cancel</a>
</div>
