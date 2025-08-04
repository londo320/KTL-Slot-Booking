<x-app-layout>
    @include('layouts.admin-nav')

    <div class="max-w-4xl mx-auto py-6">
        <h2 class="text-xl font-bold mb-4">Depot Products for {{ $depot->name }}</h2>

        <form method="POST" action="{{ route('admin.depots.products.store', $depot) }}" class="mb-6 bg-white p-4 rounded shadow space-y-4">
            @csrf
            <div>
                <label class="block font-medium">Product (SKU)</label>
                <select name="product_id" class="border p-2 w-full">
                    @foreach($allProducts as $product)
                        <option value="{{ $product->id }}">{{ $product->sku }} — {{ $product->description }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block">Min Cases</label>
                    <input type="number" name="min_cases" class="w-full border p-2" min="0">
                </div>
                <div>
                    <label class="block">Max Cases</label>
                    <input type="number" name="max_cases" class="w-full border p-2" min="0">
                </div>
                <div>
                    <label class="block">Override Duration (minutes)</label>
                    <input type="number" name="duration_override_minutes" class="w-full border p-2" min="0" step="15">
                </div>
            </div>

            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                ➕ Add Product to Depot
            </button>
        </form>

        <table class="w-full text-sm bg-white shadow rounded">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="p-2">SKU</th>
                    <th class="p-2">Description</th>
                    <th class="p-2">Min</th>
                    <th class="p-2">Max</th>
                    <th class="p-2">Override</th>
                </tr>
            </thead>
            <tbody>
                @foreach($depot->products as $product)
                    <tr class="border-t">
                        <td class="p-2">{{ $product->sku }}</td>
                        <td class="p-2">{{ $product->description }}</td>
                        <td class="p-2">{{ $product->pivot->min_cases }}</td>
                        <td class="p-2">{{ $product->pivot->max_cases }}</td>
                        <td class="p-2">{{ $product->pivot->duration_override_minutes ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
