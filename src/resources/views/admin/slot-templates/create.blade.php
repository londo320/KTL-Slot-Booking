@extends('layouts.admin')

@section('content')
<div class="p-6 max-w-md mx-auto space-y-4">
  <h1 class="text-xl">New Slot Template</h1>
  <form method="POST" action="{{ route('admin.slot-templates.store') }}" class="space-y-3">
    @csrf

    <div>
      <label>Depot</label>
      <select name="depot_id">
        @foreach($depots as $d)
          <option value="{{ $d->id }}" @selected(old('depot_id')==$d->id)>
            {{ $d->name }}
          </option>
        @endforeach
      </select>
      @error('depot_id')<div class="text-red-600">{{ $message }}</div>@enderror
    </div>

    <div>
      <label>Weekday (0=Sun…6=Sat)</label>
      <input type="number" name="weekday" min="0" max="6" value="{{ old('weekday') }}">
      @error('weekday')<div class="text-red-600">{{ $message }}</div>@enderror
    </div>

    <div>
      <label>Start Time</label>
      <input type="time" name="start_time" value="{{ old('start_time') }}">
      @error('start_time')<div class="text-red-600">{{ $message }}</div>@enderror
    </div>

    <div>
      <label>Booking Type</label>
      <select name="booking_type_id">
        @foreach($types as $t)
          <option value="{{ $t->id }}" @selected(old('booking_type_id')==$t->id)>
            {{ $t->name }}
          </option>
        @endforeach
      </select>
      @error('booking_type_id')<div class="text-red-600">{{ $message }}</div>@enderror
    </div>

    <div>
      <label>Default Length (minutes)</label>
      <input type="number" name="default_length" min="1" value="{{ old('default_length',60) }}">
      @error('default_length')<div class="text-red-600">{{ $message }}</div>@enderror
    </div>

    <button class="bg-green-600 text-white px-4 py-2 rounded">Save</button>
  </form>
</div>
@endsection
