@extends('layouts.admin')

@section('content')
<div class="py-6 max-w-4xl mx-auto">
  <h2 class="text-2xl font-semibold mb-4">New Customer</h2>

  @if($errors->any())
    <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
      <ul class="list-disc list-inside">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin.customers.store') }}" method="POST" class="space-y-4 bg-white shadow rounded p-6">
    @csrf

    <div>
      <label for="name" class="block text-sm font-medium">Name</label>
      <input type="text"
             name="name"
             id="name"
             value="{{ old('name') }}"
             class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
      @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Emails --}}
    <div>
      <label for="emails" class="block text-sm font-medium">Contact Emails (comma-separated)</label>
      <input type="text"
             name="emails"
             id="emails"
             value="{{ old('emails') }}"
             placeholder="e.g. alice@example.com, bob@example.com"
             class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
      @error('emails') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Assign exactly one user --}}
    <div>
      <label for="user_id" class="block text-sm font-medium">Assign User</label>
      <select name="user_id"
              id="user_id"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
              required>
        <option value="">— Select a user —</option>
        @foreach($users as $user)
          <option value="{{ $user->id }}"
            {{ old('user_id') == $user->id ? 'selected' : '' }}>
            {{ $user->name }}
          </option>
        @endforeach
      </select>
      @error('user_id')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
      @enderror
    </div>

    <div class="flex justify-end">
      <button type="submit"
              class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        Create Customer
      </button>
    </div>
  </form>
</div>
@endsection
