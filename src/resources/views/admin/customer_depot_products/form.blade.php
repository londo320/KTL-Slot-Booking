@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    {{-- Customer --}}
    <div>
        <label for="customer_id" class="block text-sm font-medium">Customer</label>
        <select name="customer_id" id="customer_id" class="mt-1 block w-full border rounded p-2">
            @foreach($customers as $c)
                <option value="{{ $c->id }}"
                    {{ old('customer_id', $rule->customer_id ?? '') == $c->id ? 'selected' : '' }}>
                    {{ $c->name }}
                </option>
            @endforeach
        </select>
        @error('customer_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>

    {{-- Depot --}}
    <div>
        <label for="depot_id" class="block text-sm font-medium">Depot</label>
        <select name="depot_id" id="depot_id" class="mt-1 block w-full border rounded p-2">
            @foreach($depots as $d)
                <option value="{{ $d->id }}"
                    {{ old('depot_id', $rule->depot_id ?? '') == $d->id ? 'selected' : '' }}>
                    {{ $d->name }}
                </option>
            @endforeach
        </select>
        @error('depot_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>

    {{-- Product --}}
    <div>
        <label for="product_id" class="block text-sm font-medium">Product</label>
        <select name="product_id" id="product_id" class="mt-1 block w-full border rounded p-2">
            @foreach($products as $p)
                <option value="{{ $p->id }}"
                    {{ old('product_id', $rule->product_id ?? '') == $p->id ? 'selected' : '' }}>
                    {{ $p->sku }}
                </option>
            @endforeach
        </select>
        @error('product_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>

    {{-- Min Cases --}}
    <div>
        <label for="min_cases" class="block text-sm font-medium">Minimum Cases</label>
        <input type="number" name="min_cases" id="min_cases"
               value="{{ old('min_cases', $rule->min_cases ?? '') }}"
               class="mt-1 block w-full border rounded p-2" />
        @error('min_cases') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>

    {{-- Max Cases --}}
    <div>
        <label for="max_cases" class="block text-sm font-medium">Maximum Cases</label>
        <input type="number" name="max_cases" id="max_cases"
               value="{{ old('max_cases', $rule->max_cases ?? '') }}"
               class="mt-1 block w-full border rounded p-2" />
        @error('max_cases') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>

    {{-- Override Duration --}}
    <div>
        <label for="override_duration_minutes" class="block text-sm font-medium">
            Override Duration (minutes)
        </label>
        <input type="number" name="override_duration_minutes" id="override_duration_minutes"
               value="{{ old('override_duration_minutes', $rule->override_duration_minutes ?? '') }}"
               class="mt-1 block w-full border rounded p-2" />
        @error('override_duration_minutes') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mt-6">
    <button type="submit"
            class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700">
        Save Rule
    </button>
    <a href="{{ route('admin.customer-depot-products.index') }}"
       class="ml-4 text-gray-600 hover:underline">Cancel</a>
</div>
