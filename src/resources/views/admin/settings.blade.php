  {{-- Admin Nav --}}
  @include('layouts.admin-nav')
{{-- resources/views/admin/settings.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Admin Settings') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto space-y-6">
        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.settings.store') }}">
            @csrf

            <div class="mb-4">
                <label for="depot_id" class="block font-medium">{{ __('Select Depot') }}</label>
                <select id="depot_id" name="depot_id" class="border rounded w-full p-2">
                    <option value="">{{ __('— choose one —') }}</option>
                    @foreach($depots as $d)
                        <option value="{{ $d->id }}" @selected(old('depot_id') == $d->id)>
                            {{ $d->name }}
                        </option>
                    @endforeach
                </select>
                @error('depot_id')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
                {{ __('Save Settings') }}
            </button>
        </form>
    </div>
</x-app-layout>
