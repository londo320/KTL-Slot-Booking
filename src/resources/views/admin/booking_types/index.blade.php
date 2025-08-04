<x-app-layout>
  {{-- Admin Nav --}}
  @include('layouts.admin-nav')

  {{-- Header --}}
  <x-slot name="header">
    <h2 class="text-xl font-semibold">📦 Booking Types</h2>
  </x-slot>

  <div class="py-6 max-w-4xl mx-auto space-y-6">

    {{-- Success Message --}}
    @if(session('success'))
      <div class="bg-green-100 text-green-800 p-4 rounded">
        {{ session('success') }}
      </div>
    @endif

    {{-- New Booking Type Form --}}
    <div class="bg-white p-6 shadow rounded">
      <form method="POST" action="{{ route('admin.booking-types.store') }}" class="space-y-4">
        @csrf
        <div>
          <label class="block font-medium text-sm">Name</label>
          <input name="name" value="{{ old('name') }}" class="border p-2 w-full rounded" />
          @error('name')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>

        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
          Add Booking Type
        </button>
      </form>
    </div>

    {{-- List of Booking Types --}}
    <div class="bg-white p-6 shadow rounded">
      <h3 class="font-semibold mb-4 text-lg">Existing Types</h3>
      <table class="min-w-full text-sm">
        <thead>
          <tr>
            <th class="text-left px-2 py-1">Name</th>
            <th class="text-left px-2 py-1">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($types as $type)
            <tr class="border-t">
              <td class="px-2 py-1">{{ $type->name }}</td>
              <td class="px-2 py-1 space-x-2">
                <a href="{{ route('admin.booking-types.edit', $type) }}"
                   class="text-blue-600 hover:underline text-sm">Edit</a>

                <form method="POST"
                      action="{{ route('admin.booking-types.destroy', $type) }}"
                      onsubmit="return confirm('Delete this booking type?');"
                      class="inline-block">
                  @csrf
                  @method('DELETE')
                  <button class="text-red-600 hover:underline text-sm">Delete</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="2" class="text-center py-4 text-gray-500">
                No booking types found.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>
</x-app-layout>
