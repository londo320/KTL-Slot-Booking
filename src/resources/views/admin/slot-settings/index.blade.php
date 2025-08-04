<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl font-semibold">Slot Generation Settings</h2>
  </x-slot>

  <div class="max-w-5xl mx-auto py-6">
    @if(session('success'))
      <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
        {{ session('success') }}
      </div>
    @endif

    <form method="POST" action="{{ route('admin.slot-settings.store') }}">
      @csrf

      @foreach($depots as $depot)
        <div class="mb-6 border-b pb-4">
          <h3 class="text-lg font-semibold mb-2">{{ $depot->name }}</h3>

          @php
            $settings = $depot->slotGenerationSetting;
          @endphp

          <div class="grid grid-cols-3 gap-4">
            <div>
              <label>Start Time</label>
              <input type="time" name="settings[{{ $depot->id }}][start_time]"
                     value="{{ old("settings.{$depot->id}.start_time", $settings?->start_time?->format('H:i') ?? '06:00') }}"
                     class="w-full border rounded p-2">
            </div>

            <div>
              <label>End Time</label>
              <input type="time" name="settings[{{ $depot->id }}][end_time]"
                     value="{{ old("settings.{$depot->id}.end_time", $settings?->end_time?->format('H:i') ?? '18:00') }}"
                     class="w-full border rounded p-2">
            </div>

            <div>
              <label>Interval (mins)</label>
              <input type="number" name="settings[{{ $depot->id }}][interval_minutes]"
                     value="{{ old("settings.{$depot->id}.interval_minutes", $settings?->interval_minutes ?? 60) }}"
                     class="w-full border rounded p-2" min="15" max="180">
            </div>

            <div>
              <label>Slots per Block</label>
              <input type="number" name="settings[{{ $depot->id }}][slots_per_block]"
                     value="{{ old("settings.{$depot->id}.slots_per_block", $settings?->slots_per_block ?? 1) }}"
                     class="w-full border rounded p-2" min="1" max="10">
            </div>

            <div>
              <label>Default Capacity</label>
              <input type="number" name="settings[{{ $depot->id }}][default_capacity]"
                     value="{{ old("settings.{$depot->id}.default_capacity", $settings?->default_capacity ?? 1) }}"
                     class="w-full border rounded p-2" min="1" max="10">
            </div>

            <div>
              <label>Days Active</label>
              <div class="flex flex-wrap gap-2 mt-1">
                @foreach(['mon','tue','wed','thu','fri','sat','sun'] as $day)
                  <label class="flex items-center gap-1">
                    <input type="checkbox"
                           name="settings[{{ $depot->id }}][days_active][]"
                           value="{{ $day }}"
                           @checked(in_array($day, $settings?->days_active ?? []))>
                    {{ ucfirst($day) }}
                  </label>
                @endforeach
              </div>
            </div>
          </div>
        </div>
      @endforeach

      <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        Save Settings
      </button>
    </form>
  </div>
</x-app-layout>
