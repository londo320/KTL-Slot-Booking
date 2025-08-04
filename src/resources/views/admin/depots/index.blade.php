  {{-- Admin Nav --}}
  @include('layouts.admin-nav')

@extends('layouts.admin')

@section('content')
<div class="py-6 max-w-4xl mx-auto">
  <h2 class="text-xl font-semibold mb-4">Depots</h2>

  <div class="mb-4">
    <a href="{{ route('admin.depots.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">+ New Depot</a>
  </div>

  @if(session('success'))
    <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
      {{ session('success') }}
    </div>
  @endif

  <table class="w-full table-auto border">
    <thead>
      <tr class="bg-gray-100">
        <th class="p-2 border">Name</th>
        <th class="p-2 border">Location</th>
        <th class="p-2 border">Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach($depots as $depot)
        <tr>
          <td class="p-2 border">{{ $depot->name }}</td>
          <td class="p-2 border">{{ $depot->location ?? '-' }}</td>
          <td class="p-2 border space-x-2">
            <a href="{{ route('admin.depots.edit', $depot) }}" class="text-blue-600">Edit</a>
            <form action="{{ route('admin.depots.destroy', $depot) }}" method="POST" class="inline">
              @csrf
              @method('DELETE')
              <button type="submit" class="text-red-600" onclick="return confirm('Are you sure?')">Delete</button>
            </form>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <div class="mt-4">
    {{ $depots->links() }}
  </div>
</div>
@endsection