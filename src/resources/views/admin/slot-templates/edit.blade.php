<x-app-layout>
    @include('layouts.admin-nav')

    <div class="py-6 max-w-xl mx-auto">
        <h2 class="text-xl font-bold mb-4">Edit Slot Template</h2>

        @php
            // Time intervals: 00:00 to 23:45 in 15-minute steps
            $times = [];
            for ($hour = 0; $hour < 24; $hour++) {
                foreach ([0, 30] as $minute) {
                    $times[] = sprintf('%02d:%02d', $hour, $minute);
                }
            }

            $selectedStart = old('start_time', \Carbon\Carbon::parse($slotTemplate->start_time)->format('H:i'));
            $selectedEnd = old('end_time', \Carbon\Carbon::parse($slotTemplate->end_time)->format('H:i'));
        @endphp

        <form method="POST" action="{{ route('admin.slot-templates.update', $slotTemplate) }}" class="bg-white p-6 rounded shadow space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-medium">Depot</label>
                <select name="depot_id" class="border p-2 w-full">
                    @foreach($depots as $d)
                        <option value="{{ $d->id }}" @selected(old('depot_id', $slotTemplate->depot_id) == $d->id)>
                            {{ $d->name }}
                        </option>
                    @endforeach
                </select>
                @error('depot_id')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block font-medium">Day of Week</label>
                <select name="day_of_week" class="border p-2 w-full">
                    @foreach([
                        1 => 'Monday',
                        2 => 'Tuesday',
                        3 => 'Wednesday',
                        4 => 'Thursday',
                        5 => 'Friday',
                        6 => 'Saturday',
                        0 => 'Sunday'
                    ] as $num => $label)
                        <option value="{{ $num }}" @selected(old('day_of_week', $slotTemplate->day_of_week) == $num)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('day_of_week')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block font-medium">Start Time</label>
                <select name="start_time" class="border p-2 w-full">
                    @foreach($times as $time)
                        <option value="{{ $time }}" @selected($selectedStart === $time)>
                            {{ $time }}
                        </option>
                    @endforeach
                </select>
                @error('start_time')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block font-medium">End Time</label>
                <select name="end_time" class="border p-2 w-full">
                    @foreach($times as $time)
                        <option value="{{ $time }}" @selected($selectedEnd === $time)>
                            {{ $time }}
                        </option>
                    @endforeach
                </select>
                @error('end_time')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
            </div>

            <div class="text-right">
                <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update</button>
                <a href="{{ route('admin.slot-templates.index') }}" class="ml-2 text-sm text-gray-600 hover:underline">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form[action*="slot-templates"]');

    if (!form) return;

    form.addEventListener('submit', function (e) {
        const startTime = form.querySelector('select[name="start_time"]').value;
        const endTime = form.querySelector('select[name="end_time"]').value;

        if (!startTime || !endTime) return; // Laravel handles empty

        const [startH, startM] = startTime.split(':').map(Number);
        const [endH, endM] = endTime.split(':').map(Number);

        const start = new Date();
        const end = new Date();

        start.setHours(startH, startM, 0);
        end.setHours(endH, endM, 0);

        let duration = (end - start) / 60000; // in minutes

        if (duration <= 0) {
            duration += 1440; // handle overnight (e.g., 23:00–01:00)
        }

        if (duration > 720) {
            e.preventDefault();
            alert("⛔ Duration must not exceed 12 hours.");
            return;
        }

        if (duration % 15 !== 0) {
            e.preventDefault();
            alert("⚠️ Duration must be in 15-minute intervals.");
            return;
        }
    });
});
</script>
