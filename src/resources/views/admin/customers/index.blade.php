@extends('layouts.admin')

@section('content')
<div class="py-6 max-w-6xl mx-auto">
  <div class="flex items-center justify-between mb-4">
    <h2 class="text-2xl font-semibold">Customers Management</h2>
    <a href="{{ route('admin.customers.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
      + New Customer
    </a>
  </div>

  @if(session('success'))
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
      {{ session('success') }}
    </div>
  @endif

  <table class="min-w-full bg-white shadow rounded overflow-hidden">
    <thead class="bg-gray-100">
      <tr>
        <th class="px-4 py-2 text-left">Name</th>
        <th class="px-4 py-2 text-left">Email</th>
        <th class="px-4 py-2 text-left">Assigned Users</th>
        <th class="px-4 py-2 text-left">Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach($customers as $customer)
        <tr class="border-t hover:bg-gray-50">
          <td class="px-4 py-2">{{ $customer->name }}</td>
          <td class="px-4 py-2">{{ $customer->email }}</td>
          <td class="px-4 py-2">
            @forelse($customer->users as $user)
              <span class="inline-block bg-gray-200 rounded-full px-2 py-1 text-xs mr-1">
                {{ $user->name }}
              </span>
            @empty
              <span class="text-gray-500 text-xs">—</span>
            @endforelse
          </td>
          <td class="px-4 py-2 space-x-2">
            <a href="{{ route('admin.customers.edit', $customer) }}"
               class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-xs">
               Edit
            </a>
            <form action="{{ route('admin.customers.destroy', $customer) }}"
                  method="POST"
                  class="inline-block"
                  onsubmit="return confirm('Delete this customer?');">
              @csrf
              @method('DELETE')
              <button type="submit"
                      class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs">
                Delete
              </button>
            </form>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <div class="p-4">
    {{ $customers->links() }}
  </div>
</div>
@endsection
