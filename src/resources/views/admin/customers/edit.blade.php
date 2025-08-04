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

  <label for="user_id" class="block text-sm font-medium">Assign User</label>
  <select name="user_id" id="user_id"
          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
    <option value="">— Select a user —</option>
    @foreach($users as $user)
      <option value="{{ $user->id }}"
        {{ old('user_id', $customer->users->first()->id ?? '') == $user->id ? 'selected' : '' }}>
        {{ $user->name }}
      </option>
    @endforeach
  </select>
  @error('user_id')
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
