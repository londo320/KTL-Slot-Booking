@extends('layouts.admin')

@section('content')
<div class="p-6 bg-white rounded shadow">
  <h2 class="text-xl mb-4">Generate Slots</h2>
  <!-- Server time/info -->
<div class="mb-4 text-sm text-gray-600">
  <p>Server Time ({{ config('app.timezone') }}): {{ \Carbon\Carbon::now()->toDateTimeString() }}</p>
</div>
  <form action="{{ route('admin.slots.generate') }}" method="POST" class="space-y-4">
    @csrf

    {{-- Depot selector --}}
    <div>
      <label for="depot_id" class="block text-sm font-medium">Select Depot</label>
      <select name="depot_id" id="depot_id" required class="mt-1 block w-1/3 border rounded p-2">
        <option value="">-- Choose a Depot --</option>
        @foreach($depots as $depot)
          <option value="{{ $depot->id }}" {{ old('depot_id') == $depot->id ? 'selected' : '' }}>
            {{ $depot->name }}
          </option>
        @endforeach
      </select>
      @error('depot_id')
        <p class="text-red-600 text-sm">{{ $message }}</p>
      @enderror
    </div>

    {{-- Date preview fields --}}
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium">Start Date</label>
        <input type="date" id="start_date" class="mt-1 block w-1/3 border rounded p-2" readonly />
      </div>
      <div>
        <label class="block text-sm font-medium">End Date</label>
        <input type="date" id="end_date" class="mt-1 block w-1/3 border rounded p-2" readonly />
      </div>
    </div>

    <div>
      <label for="days" class="block text-sm font-medium">Generate Ahead (days)</label>
      <input type="number" name="days" id="days_input" placeholder="Leave blank for default" class="mt-1 block w-1/3 border rounded p-2" />
      @error('days')
        <p class="text-red-600 text-sm">{{ $message }}</p>
      @enderror
    </div>

    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded">Run Generator</button>
  </form>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const daysInput = document.getElementById('days_input');
    const startDateField = document.getElementById('start_date');
    const endDateField = document.getElementById('end_date');
    const today = new Date();

    // Initialize dates
    const formatDate = d => d.toISOString().split('T')[0];
    startDateField.value = formatDate(today);
    updateEndDate();

    daysInput.addEventListener('input', updateEndDate);

    function updateEndDate() {
      const days = parseInt(daysInput.value, 10);
      const offset = isNaN(days) ? {{ config('slots.default_generate_days', 14) }} : days;
      const end = new Date(today);
      end.setDate(end.getDate() + offset);
      endDateField.value = formatDate(end);
    }
  });
</script>
@endsection