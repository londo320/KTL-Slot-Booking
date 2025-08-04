<div class="grid grid-cols-2 gap-6">

  <div>
    <label class="block text-sm font-medium">Slot</label>
    <select name="slot_id" required class="mt-1 block w-full border-gray-300 rounded">
      <option value="">– Choose slot –</option>
      @foreach($slots as $slot)
        <option value="{{ $slot->id }}"
          @selected(old('slot_id', $booking->slot_id) == $slot->id)>
          {{ $slot->depot->name }}
          ({{ \Carbon\Carbon::parse($slot->start_at)->format('d-M H:i') }} → {{ \Carbon\Carbon::parse($slot->end_at)->format('d-M H:i') }})
        </option>
      @endforeach
    </select>
    @error('slot_id')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
  </div>

  <div>
    <label class="block text-sm font-medium">Booking Type</label>
    <select name="booking_type_id" required class="mt-1 block w-full border-gray-300 rounded">
      <option value="">– Choose type –</option>
      @foreach($types as $type)
        <option value="{{ $type->id }}"
          @selected(old('booking_type_id', $booking->booking_type_id) == $type->id)>
          {{ $type->name }}
        </option>
      @endforeach
    </select>
    @error('booking_type_id')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
  </div>

  <div>
    <label class="block text-sm font-medium">Expected Cases</label>
    <input type="number" name="expected_cases"
           value="{{ old('expected_cases', $booking->expected_cases) }}"
           class="mt-1 block w-full border-gray-300 rounded">
    @error('expected_cases')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
  </div>

  <div>
    <label class="block text-sm font-medium">Expected Pallets</label>
    <input type="number" name="expected_pallets"
           value="{{ old('expected_pallets', $booking->expected_pallets) }}"
           class="mt-1 block w-full border-gray-300 rounded">
    @error('expected_pallets')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
  </div>

  <div class="col-span-2">
    <label class="block text-sm font-medium">Container Size (kg)</label>
    <input type="number" name="container_size"
           value="{{ old('container_size', $booking->container_size) }}"
           class="mt-1 block w-full border-gray-300 rounded">
    @error('container_size')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
  </div>

  <div class="col-span-2">
    <label class="block text-sm font-medium">Reference</label>
    <input type="text" name="reference"
           value="{{ old('reference', $booking->reference) }}"
           class="mt-1 block w-full border-gray-300 rounded">
    @error('reference')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
  </div>

  <!-- Transportation Information Section - READ ONLY -->
  @if($booking->vehicle_registration || $booking->container_number || $booking->driver_name || $booking->carrier_company || $booking->load_type || $booking->hazmat || $booking->temperature_requirements || $booking->estimated_arrival || $booking->special_instructions)
  <div class="col-span-2 border-t pt-4 mt-4">
    <h3 class="text-lg font-medium text-gray-900 mb-4">🚛 Transportation Information (Read Only)</h3>
    <div class="bg-gray-50 p-4 rounded-lg">
      <div class="grid grid-cols-2 gap-4 text-sm">
        
        @if($booking->vehicle_registration)
        <div>
          <label class="block text-sm font-medium text-gray-600">Vehicle Registration</label>
          <div class="mt-1 text-gray-900">{{ $booking->vehicle_registration }}</div>
        </div>
        @endif

        @if($booking->container_number)
        <div>
          <label class="block text-sm font-medium text-gray-600">Container Number</label>
          <div class="mt-1 text-gray-900">{{ $booking->container_number }}</div>
        </div>
        @endif

        @if($booking->driver_name)
        <div>
          <label class="block text-sm font-medium text-gray-600">Driver Name</label>
          <div class="mt-1 text-gray-900">{{ $booking->driver_name }}</div>
        </div>
        @endif

        @if($booking->driver_phone)
        <div>
          <label class="block text-sm font-medium text-gray-600">Driver Phone</label>
          <div class="mt-1 text-gray-900">{{ $booking->driver_phone }}</div>
        </div>
        @endif

        @if($booking->carrier_company)
        <div>
          <label class="block text-sm font-medium text-gray-600">Carrier Company</label>
          <div class="mt-1 text-gray-900">{{ $booking->carrier_company }}</div>
        </div>
        @endif

        @if($booking->load_type)
        <div>
          <label class="block text-sm font-medium text-gray-600">Load Type</label>
          <div class="mt-1 text-gray-900">{{ $booking->load_type }}</div>
        </div>
        @endif

        @if($booking->hazmat)
        <div class="col-span-2">
          <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
            ⚠️ Hazardous Materials (HAZMAT)
          </span>
        </div>
        @endif

        @if($booking->temperature_requirements)
        <div>
          <label class="block text-sm font-medium text-gray-600">Temperature Requirements</label>
          <div class="mt-1 text-gray-900">{{ $booking->temperature_requirements }}</div>
        </div>
        @endif

        @if($booking->estimated_arrival)
        <div>
          <label class="block text-sm font-medium text-gray-600">Estimated Arrival</label>
          <div class="mt-1 text-gray-900">{{ $booking->estimated_arrival->format('d-M-Y H:i') }}</div>
        </div>
        @endif

      </div>
      
      @if($booking->special_instructions)
      <div class="mt-4">
        <label class="block text-sm font-medium text-gray-600">Special Instructions</label>
        <div class="mt-1 text-gray-900 p-3 bg-white rounded border">{{ $booking->special_instructions }}</div>
      </div>
      @endif
      
      <div class="mt-4 p-3 bg-blue-50 rounded text-sm text-blue-700">
        <strong>Note:</strong> Transportation details can only be updated by depot staff during arrival processing.
      </div>
    </div>
  </div>
  @endif

  <div class="col-span-2">
    <label class="block text-sm font-medium">Notes</label>
    <textarea name="notes" rows="3"
              class="mt-1 block w-full border-gray-300 rounded">{{ old('notes', $booking->notes) }}</textarea>
    @error('notes')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
  </div>
</div>