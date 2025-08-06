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
            {{ in_array($user->id, old('user_ids', [])) ? 'selected' : '' }}>
            {{ $user->name }} ({{ $user->email }})
          </option>
        @endforeach
      </select>
      <p class="text-sm text-gray-600 mt-1">Hold Ctrl/Cmd to select multiple users. Leave empty if no users need assignment.</p>
      @error('user_ids')
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
