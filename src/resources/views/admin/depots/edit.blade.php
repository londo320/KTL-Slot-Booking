@extends('layouts.admin')

@section('content')
<div class="py-6 max-w-4xl mx-auto">
  <h2 class="text-xl font-semibold mb-4">Edit Depot</h2>

  @if($errors->any())
    <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
      <ul class="list-disc list-inside">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin.depots.update', $depot) }}" method="POST" class="space-y-4 bg-white shadow rounded p-6">
    @csrf
    @method('PATCH')

    <div>
      <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
      <input
        type="text"
        name="name"
        id="name"
        value="{{ old('name', $depot->name) }}"
        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
        required
      >
      @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
      <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
      <textarea
        name="description"
        id="description"
        rows="3"
        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
      >{{ old('description', $depot->description) }}</textarea>
      @error('description')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
      <label for="cut_off_time" class="block text-sm font-medium text-gray-700">Cut Off Time</label>
      <input
        type="time"
        name="cut_off_time"
        id="cut_off_time"
        value="{{ old('cut_off_time', $depot->cut_off_time) }}"
        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
        pattern="[0-9]{2}:[0-9]{2}"
        placeholder="HH:MM"
        required
      >
      <p class="text-gray-500 text-sm mt-1">Format: HH:MM (24-hour)</p>
      @error('cut_off_time')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="flex justify-end space-x-2">
      <a href="{{ route('admin.depots.index') }}"
         class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
        Cancel
      </a>
      <button type="submit"
              class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        Save Changes
      </button>
    </div>
  </form>
</div>
@endsection