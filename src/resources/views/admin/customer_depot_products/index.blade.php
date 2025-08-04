@extends('layouts.admin')

@section('content')
<div class="p-6 bg-white rounded-lg shadow">
    <h1 class="text-2xl font-semibold mb-4">Customer-Depot-Product Rules</h1>

    <a href="{{ route('admin.customer-depot-products.create') }}"
       class="inline-block mb-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        + New Rule
    </a>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    <table class="w-full table-auto border-collapse">
        <thead>
            <tr class="bg-gray-100">
                <th class="px-4 py-2 text-left">Customer</th>
                <th class="px-4 py-2 text-left">Depot</th>
                <th class="px-4 py-2 text-left">Product</th>
                <th class="px-4 py-2 text-center">Min Cases</th>
                <th class="px-4 py-2 text-center">Max Cases</th>
                <th class="px-4 py-2 text-center">Override (min)</th>
                <th class="px-4 py-2 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $rule)
            <tr class="border-t">
                <td class="px-4 py-2">{{ $rule->customer->name }}</td>
                <td class="px-4 py-2">{{ $rule->depot->name }}</td>
                <td class="px-4 py-2">{{ $rule->product->sku }}</td>
                <td class="px-4 py-2 text-center">{{ $rule->min_cases ?? '–' }}</td>
                <td class="px-4 py-2 text-center">{{ $rule->max_cases ?? '–' }}</td>
                <td class="px-4 py-2 text-center">{{ $rule->override_duration_minutes ?? '–' }}</td>
                <td class="px-4 py-2 text-right">
                    <a href="{{ route('admin.customer-depot-products.edit', $rule) }}"
                       class="mr-2 text-blue-600 hover:underline">Edit</a>
                    <form action="{{ route('admin.customer-depot-products.destroy', $rule) }}"
                          method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="text-red-600 hover:underline"
                                onclick="return confirm('Delete this rule?')">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
