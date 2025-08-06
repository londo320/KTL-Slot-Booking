@extends('layouts.admin')

@section('content')
<div class="py-6 max-w-4xl mx-auto">
  <h2 class="text-2xl font-semibold mb-4">Edit Customer</h2>

  @if($errors->any())
    <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
      <ul class="list-disc list-inside">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin.customers.update', $customer) }}" method="POST" class="space-y-4 bg-white shadow rounded p-6">
    @csrf
    @method('PATCH')

    <div>
      <label for="name" class="block text-sm font-medium">Name</label>
      <input type="text" name="name" id="name"
             value="{{ old('name', $customer->name) }}"
             class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
      @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Assign multiple users (optional) --}}
    <div>
      <label for="user_ids" class="block text-sm font-medium">Assign Users (Optional)</label>
      <select name="user_ids[]"
              id="user_ids"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
              multiple
              size="6">
        @foreach($users as $user)
          <option value="{{ $user->id }}"
            {{ in_array($user->id, old('user_ids', $customer->users->pluck('id')->toArray())) ? 'selected' : '' }}>
            {{ $user->name }} ({{ $user->email }})
          </option>
        @endforeach
      </select>
      <p class="text-sm text-gray-600 mt-1">Hold Ctrl/Cmd to select multiple users. Leave empty if no users need assignment.</p>
      @error('user_ids')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
      @enderror
    </div>

    <div class="flex justify-end space-x-2">
      <a href="{{ route('admin.customers.index') }}"
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
