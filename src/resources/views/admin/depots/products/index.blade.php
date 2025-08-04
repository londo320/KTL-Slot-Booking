<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl font-semibold">🛠 Manage Products for Depot: {{ $depot->name }}</h2>
  </x-slot>

  <div class="max-w-5xl mx-auto py-6 space-y-6">

    @if(session('success'))
      <div class="bg-green-100 p-3 rounded text-green-800">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.depots.products.update', $depot) }}">
      @csrf

      <table class="min-w-full text-sm border">
        <thead>
          <tr class="bg-gray-100">
            <th class="p-2 text-left">SKU</th>
            <th class="p-2 text-left">Description</th>
            <th class="p-2 text-center">Min Cases</th>
            <th class="p-2 text-center">Max Cases</th>
            <th class="p-2 text-center">Duration Override (mins)</th>
            <th class="p-2 text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($products as $product)
            <tr class="border-t">
              <td class="p-2">{{ $product->sku }}</td>
              <td class="p-2">{{ $product->description }}</td>

              <td class="p-2 text-center">
                <input type="number" name="products[{{ $product->id }}][min_cases]" class="border p-1 w-20 text-center"
                       value="{{ old("products.{$product->id}.min_cases", $assigned[$product->id]->pivot->min_cases ?? '') }}">
              </td>

              <td class="p-2 text-center">
                <input type="number" name="products[{{ $product->id }}][max_cases]" class="border p-1 w-20 text-center"
                       value="{{ old("products.{$product->id}.max_cases", $assigned[$product->id]->pivot->max_cases ?? '') }}">
              </td>

              <td class="p-2 text-center">
                <input type="number" name="products[{{ $product->id }}][duration_override_minutes]" class="border p-1 w-24 text-center"
                       value="{{ old("products.{$product->id}.duration_override_minutes", $assigned[$product->id]->pivot->duration_override_minutes ?? '') }}">
              </td>

              <td class="p-2 text-center">
                @if(isset($assigned[$product->id]))
                  <form method="POST" action="{{ route('admin.depots.products.destroy', [$depot, $product]) }}"
                        onsubmit="return confirm('Remove this product from depot?');" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button class="text-red-600 hover:underline text-xs" type="submit">Remove</button>
                  </form>
                @else
                  <span class="text-gray-400 text-xs">Not assigned</span>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <div class="mt-4">
        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">💾 Save Changes</button>
      </div>
    </form>
  </div>
</x-app-layout>
