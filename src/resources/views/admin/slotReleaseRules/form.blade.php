@extends('layouts.admin')

@section('content')
<div class="py-6 max-w-4xl mx-auto">
  @if(session('success'))
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
      {{ session('success') }}
    </div>
  @endif

  <h1 class="text-2xl font-semibold mb-6">
    {{ isset($rule->id) ? 'Edit' : 'Create' }} Slot Release Rule
  </h1>

  <form method="POST" action="{{ isset($rule->id) ? route('admin.slotReleaseRules.update', $rule) : route('admin.slotReleaseRules.store') }}">
    @csrf
    @if(isset($rule->id))
      @method('PUT')
    @endif

    <!-- Depot Selection -->
    <div class="mb-4">
      <label for="depot_id" class="block text-sm font-medium">
        Depot
        <span class="text-gray-500 text-xs italic">Select the warehouse location this rule applies to.</span>
      </label>
      <select name="depot_id" id="depot_id" class="mt-1 block w-full border-gray-300 rounded">
        <option value="">– Choose depot –</option>
        @foreach($depots as $id => $name)
          <option value="{{ $id }}" @selected(old('depot_id', $rule->depot_id) == $id)>{{ $name }}</option>
        @endforeach
      </select>
      @error('depot_id')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
    </div>

    <!-- Customers (many-to-many) -->
    <div class="mb-4">
      <label class="block text-sm font-medium">
        Customers (optional)
        <span class="text-gray-500 text-xs italic">Select which customers this rule applies to; leave none to apply to all.</span>
      </label>
      @php
        $selected = old('customer_ids', isset($rule->id) ? $rule->customers->pluck('id')->toArray() : []);
      @endphp
      <div class="mt-2 grid grid-cols-2 gap-4">
        @foreach($customers as $id => $name)
          <label class="inline-flex items-center">
            <input type="checkbox" name="customer_ids[]" value="{{ $id }}" class="form-checkbox" @checked(in_array($id, $selected))>
            <span class="ml-2 text-sm">{{ $name }}</span>
          </label>
        @endforeach
      </div>
      @error('customer_ids')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
    </div>

    <!-- Release Day -->
    <div class="mb-4">
      <label for="release_day" class="block text-sm font-medium">
        Release Day
        <span class="text-gray-500 text-xs italic">Choose the weekday when slots become available for booking.</span>
      </label>
      @php
        $days = [1=>'Mon',2=>'Tue',3=>'Wed',4=>'Thu',5=>'Fri',6=>'Sat',7=>'Sun'];
      @endphp
      <select name="release_day" id="release_day" class="mt-1 block w-full border-gray-300 rounded">
        <option value="">– Select day –</option>
        @foreach($days as $num => $abbr)
          <option value="{{ $num }}" @selected(old('release_day', $rule->release_day) == $num)>{{ $abbr }}</option>
        @endforeach
      </select>
      @error('release_day')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
    </div>

    <!-- Release Time -->
    <div class="mb-4">
      <label for="release_time" class="block text-sm font-medium">
        Release Time
        <span class="text-gray-500 text-xs italic">Time of day (HH:MM) when slots go live on the release day.</span>
      </label>
      <input type="time" name="release_time" id="release_time" value="{{ old('release_time', substr($rule->release_time, 0, 5)) }}" class="mt-1 block w-32 border-gray-300 rounded">
      @error('release_time')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
    </div>

    <!-- Lock Cutoff Days & Time -->
    <div class="grid grid-cols-2 gap-4 mb-4">
      <div>
        <label for="lock_cutoff_days" class="block text-sm font-medium">
          Cutoff Days
          <span class="text-gray-500 text-xs italic">How many days before the slot date changes are locked.</span>
        </label>
        <input type="number" name="lock_cutoff_days" id="lock_cutoff_days" value="{{ old('lock_cutoff_days', $rule->lock_cutoff_days) }}" min="0" class="mt-1 block w-24 border-gray-300 rounded">
        @error('lock_cutoff_days')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
      </div>
      <div>
        <label for="lock_cutoff_time" class="block text-sm font-medium">
          Cutoff Time
          <span class="text-gray-500 text-xs italic">Time on the cutoff days when edits are no longer allowed.</span>
        </label>
        <input type="time" name="lock_cutoff_time" id="lock_cutoff_time" value="{{ old('lock_cutoff_time', substr($rule->lock_cutoff_time, 0, 5)) }}" class="mt-1 block w-32 border-gray-300 rounded">
        @error('lock_cutoff_time')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
      </div>
    </div>

    <!-- Priority -->
    <div class="mb-4">
      <label for="priority" class="block text-sm font-medium">
        Priority
        <span class="text-gray-500 text-xs italic">Higher number gives this rule precedence when multiple apply.</span>
      </label>
      <input type="number" name="priority" id="priority" value="{{ old('priority', $rule->priority) }}" min="0" class="mt-1 block w-24 border-gray-300 rounded">
      @error('priority')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
    </div>

    <!-- Submit -->
    <div class="mt-6">
      <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        {{ isset($rule->id) ? 'Update Rule' : 'Create Rule' }}
      </button>
    </div>
  </form>
</div>
@endsection
