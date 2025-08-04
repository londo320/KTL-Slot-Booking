@extends('layouts.admin')

@section('content')
<div class="py-6 max-w-7xl mx-auto">
    {{-- Success Message --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Display the new password if it's passed to the view --}}
    @if(session('new_password'))
        <div class="mb-4 p-3 bg-yellow-100 text-yellow-800 rounded">
            <strong>Your new password is:</strong> {{ session('new_password') }}
        </div>
    @endif

    {{-- User Edit Form --}}
    <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
        @csrf
        @method('PUT')

        {{-- Name Field --}}
        <div class="flex flex-col mb-4">
            <label for="name" class="text-sm font-medium">Name</label>
            <input type="text" id="name" name="name" class="border rounded px-3 py-2 mt-1" value="{{ old('name', $user->name) }}" required>
            @error('name')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>

        {{-- Email Field --}}
        <div class="flex flex-col mb-4">
            <label for="email" class="text-sm font-medium">Email</label>
            <input type="email" id="email" name="email" class="border rounded px-3 py-2 mt-1" value="{{ old('email', $user->email) }}" required>
            @error('email')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>



{{-- Role Selection --}}
<div class="flex flex-col mb-4">
    <label class="text-sm font-medium">Assign Roles</label>
    @foreach($roles as $role)
        <div class="flex items-center mt-2">
            <input
                type="checkbox"
                id="role_{{ $role->id }}"
                name="role_ids[]"
                value="{{ $role->id }}"
                class="mr-2"
                {{ in_array($role->id, old('role_ids', $user->roles->pluck('id')->toArray())) ? 'checked' : '' }}
            >
            <label for="role_{{ $role->id }}" class="text-sm">{{ $role->name }}</label>
        </div>
    @endforeach
    @error('role_ids')
        <span class="text-red-600 text-sm">{{ $message }}</span>
    @enderror
</div>


        {{-- Depots Selection --}}
        <div class="flex flex-col mb-4">
            <label for="depot_ids" class="text-sm font-medium">Assign Depots</label>
            @foreach($depots as $depot)
                <div class="flex items-center mt-2">
                    <input type="checkbox" id="depot_{{ $depot->id }}" name="depot_ids[]" value="{{ $depot->id }}" class="mr-2"
                    {{ in_array($depot->id, old('depot_ids', $user->depots->pluck('id')->toArray())) ? 'checked' : '' }}>
                    <label for="depot_{{ $depot->id }}" class="text-sm">{{ $depot->name }}</label>
                </div>
            @endforeach
            @error('depot_ids')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>

        {{-- Customer Assignment --}}
        <div class="flex flex-col mb-4">
            <label class="text-sm font-medium mb-2">Customer Assignment</label>
            
            {{-- Legacy customer_id field for customer role --}}
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-600">Legacy Customer (for customer role)</label>
                <select name="customer_id" class="border rounded px-3 py-2 mt-1 w-full">
                    <option value="">— No Legacy Customer —</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" 
                            @selected($customer->id == old('customer_id', $user->customer_id))>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Only used for users with customer role</p>
                @error('customer_id')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
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
                                {{ in_array($customer->id, old('customer_ids', $user->customers->pluck('id')->toArray())) ? 'checked' : '' }}
                            >
                            <span class="ml-2 text-sm">{{ $customer->name }}</span>
                        </label>
                    @endforeach
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    Leave empty for admin/site roles to see ALL customers (including future ones).
                    Select specific customers to limit access.
                </p>
                @error('customer_ids')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
        </div>

        {{-- Reset Password Option --}}
        <div class="flex items-center mb-4">
            <input type="checkbox" name="reset_password" id="reset_password" class="mr-2">
            <label for="reset_password" class="text-sm">Reset to Default Password</label>
        </div>

        {{-- Submit Button --}}
        <div class="flex justify-end mt-4">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save Changes</button>
        </div>
    </form>
</div>
@endsection
