@extends('layouts.admin')

@section('content')
<div class="py-6 max-w-3xl mx-auto bg-white p-6 rounded shadow">
  {{-- Success & Password --}}
  @if(session('success'))
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
      {{ session('success') }}
    </div>
  @endif
  @if(session('new_password'))
    <div class="mb-4 p-3 bg-yellow-100 text-yellow-800 rounded">
      <strong>Your new password is:</strong> {{ session('new_password') }}
    </div>
  @endif

  <form action="{{ route('admin.users.store') }}" method="POST">
    @csrf

    {{-- Name --}}
    <div class="mb-4">
      <label class="block text-sm font-medium">Name</label>
      <input type="text" name="name"
             value="{{ old('name') }}"
             class="mt-1 block w-full border-gray-300 rounded">
      @error('name')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
    </div>

    {{-- Email --}}
    <div class="mb-4">
      <label class="block text-sm font-medium">Email</label>
      <input type="email" name="email"
             value="{{ old('email') }}"
             class="mt-1 block w-full border-gray-300 rounded">
      @error('email')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
    </div>

    {{-- Roles --}}
    <div class="mb-4">
      <label class="block text-sm font-medium">Assign Roles</label>
      <div class="mt-1 space-y-2">
        @foreach($roles as $role)
          <label class="inline-flex items-center">
            <input
              type="checkbox"
              name="role_ids[]"
              value="{{ $role->id }}"
              class="border-gray-300 rounded"
              {{ in_array($role->id, old('role_ids', [])) ? 'checked' : '' }}
            >
            <span class="ml-2 text-sm">{{ $role->name }}</span>
          </label>
        @endforeach
      </div>
      @error('role_ids')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
    </div>

    {{-- Depots --}}
    <div class="mb-4">
      <label class="block text-sm font-medium">Assign Depots</label>
      <div class="mt-1 space-y-2">
        @foreach($depots as $depot)
          <label class="inline-flex items-center">
            <input
              type="checkbox"
              name="depot_ids[]"
              value="{{ $depot->id }}"
              class="border-gray-300 rounded"
              {{ in_array($depot->id, old('depot_ids', [])) ? 'checked' : '' }}
            >
            <span class="ml-2 text-sm">{{ $depot->name }}</span>
          </label>
        @endforeach
      </div>
      @error('depot_ids')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
    </div>

    {{-- Customer Assignment --}}
    <div class="mb-4">
      <label class="block text-sm font-medium mb-2">Customer Assignment</label>
      
      {{-- Legacy customer_id field for customer role --}}
      <div class="mb-3" id="legacy-customer-field">
        <label class="block text-sm font-medium text-gray-600">Legacy Customer (for customer role)</label>
        <select name="customer_id" class="mt-1 block w-full border-gray-300 rounded">
          <option value="">— No Legacy Customer —</option>
          @foreach($customers as $customer)
            <option value="{{ $customer->id }}"
              @selected($customer->id == old('customer_id'))>
              {{ $customer->name }}
            </option>
          @endforeach
        </select>
        <p class="text-xs text-gray-500 mt-1">Only used for users with customer role</p>
        @error('customer_id')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
      </div>

      {{-- Multiple customers assignment --}}
      <div class="mb-3">
        <label class="block text-sm font-medium">Multiple Customers (for admin/site roles)</label>
        <div class="mt-2 space-y-2 max-h-40 overflow-y-auto border rounded p-2">
          @foreach($customers as $customer)
            <label class="inline-flex items-center w-full">
              <input
                type="checkbox"
                name="customer_ids[]"
                value="{{ $customer->id }}"
                class="border-gray-300 rounded"
                {{ in_array($customer->id, old('customer_ids', [])) ? 'checked' : '' }}
              >
              <span class="ml-2 text-sm">{{ $customer->name }}</span>
            </label>
          @endforeach
        </div>
        <p class="text-xs text-gray-500 mt-1">
          Leave empty for admin/site roles to see ALL customers (including future ones).
          Select specific customers to limit access.
        </p>
        @error('customer_ids')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
      </div>
    </div>

{{-- Password OR Generate --}}
<div class="mb-4 flex items-center space-x-4">
  {{-- Manual Password --}}
  <div class="flex-1">
    <label class="block text-sm font-medium">Password</label>
    <input type="password" name="password"
           value="{{ old('password') }}"
           class="mt-1 block w-full border-gray-300 rounded"
           placeholder="Enter a password">
    @error('password') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
  </div>

  {{-- OR Separator --}}
  <div class="flex items-center text-gray-500">OR</div>

  {{-- Generate Checkbox --}}
  <div class="flex items-center">
    <input type="checkbox" name="generate_password" id="generate_password"
           value="1" class="mr-2" {{ old('generate_password') ? 'checked' : '' }}>
    <label for="generate_password" class="text-sm">Generate random password</label>
    @error('generate_password') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
  </div>
</div>

    {{-- Submit --}}
    <div class="flex justify-end">
      <button type="submit"
              class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        Create User
      </button>
    </div>
  </form>
</div>
@endsection
