<div class="grid grid-cols-2 gap-6">

  <div>
    <label class="block text-sm font-medium">Available Slots</label>
    <select name="slot_id" required class="mt-1 block w-full border-gray-300 rounded">
      <option value="">– Choose slot –</option>
      {{-- Slots will be loaded dynamically via JavaScript --}}
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

  <!-- Transportation Information Section -->
  <div class="col-span-2 border-t pt-4 mt-4">
    <h3 class="text-lg font-medium text-gray-900 mb-4">🚛 Transportation Information</h3>
    <div class="grid grid-cols-2 gap-4">
      
      <div>
        <label class="block text-sm font-medium">Vehicle Registration</label>
        <input type="text" name="vehicle_registration"
               value="{{ old('vehicle_registration', $booking->vehicle_registration) }}"
               placeholder="e.g., AB12 CDE"
               class="mt-1 block w-full border-gray-300 rounded">
        @error('vehicle_registration')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
      </div>

      <div>
        <label class="block text-sm font-medium">Container Number</label>
        <input type="text" name="container_number"
               value="{{ old('container_number', $booking->container_number) }}"
               placeholder="e.g., CONT123456 or TR123456"
               class="mt-1 block w-full border-gray-300 rounded">
        @error('container_number')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
      </div>

      <div>
        <label class="block text-sm font-medium">Driver Name</label>
        <input type="text" name="driver_name"
               value="{{ old('driver_name', $booking->driver_name) }}"
               class="mt-1 block w-full border-gray-300 rounded">
        @error('driver_name')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
      </div>

      <div>
        <label class="block text-sm font-medium">Driver Phone</label>
        <input type="tel" name="driver_phone"
               value="{{ old('driver_phone', $booking->driver_phone) }}"
               placeholder="e.g., +44 7XXX XXXXXX"
               class="mt-1 block w-full border-gray-300 rounded">
        @error('driver_phone')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
      </div>

      <div>
        <label class="block text-sm font-medium">Carrier Company</label>
        <input type="text" name="carrier_company"
               value="{{ old('carrier_company', $booking->carrier_company) }}"
               class="mt-1 block w-full border-gray-300 rounded">
        @error('carrier_company')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
      </div>

      <div>
        <label class="block text-sm font-medium">Load Type</label>
        <select name="load_type" class="mt-1 block w-full border-gray-300 rounded">
          <option value="">– Select load type –</option>
          <option value="General" @selected(old('load_type', $booking->load_type) == 'General')>General</option>
          <option value="Fragile" @selected(old('load_type', $booking->load_type) == 'Fragile')>Fragile</option>
          <option value="Frozen" @selected(old('load_type', $booking->load_type) == 'Frozen')>Frozen</option>
          <option value="Chilled" @selected(old('load_type', $booking->load_type) == 'Chilled')>Chilled</option>
          <option value="Hazardous" @selected(old('load_type', $booking->load_type) == 'Hazardous')>Hazardous</option>
        </select>
        @error('load_type')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
      </div>

      <div class="col-span-2">
        <label class="flex items-center">
          <input type="checkbox" name="hazmat" value="1" 
                 @checked(old('hazmat', $booking->hazmat))
                 class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200">
          <span class="ml-2 text-sm font-medium">⚠️ Hazardous Materials (HAZMAT)</span>
        </label>
        @error('hazmat')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
      </div>

      <div>
        <label class="block text-sm font-medium">Temperature Requirements</label>
        <input type="text" name="temperature_requirements"
               value="{{ old('temperature_requirements', $booking->temperature_requirements) }}"
               placeholder="e.g., -18°C to -15°C"
               class="mt-1 block w-full border-gray-300 rounded">
        @error('temperature_requirements')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
      </div>

      <div>
        <label class="block text-sm font-medium">Estimated Arrival</label>
        <input type="datetime-local" name="estimated_arrival"
               value="{{ old('estimated_arrival', $booking->estimated_arrival?->format('Y-m-d\TH:i')) }}"
               class="mt-1 block w-full border-gray-300 rounded">
        @error('estimated_arrival')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
      </div>

    </div>
  </div>

  <div class="col-span-2">
    <label class="block text-sm font-medium">Special Instructions</label>
    <textarea name="special_instructions" rows="3"
              placeholder="Any special handling requirements, delivery instructions, or notes..."
              class="mt-1 block w-full border-gray-300 rounded">{{ old('special_instructions', $booking->special_instructions) }}</textarea>
    @error('special_instructions')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
  </div>

  <div class="col-span-2">
    <label class="block text-sm font-medium">Notes</label>
    <textarea name="notes" rows="3"
              class="mt-1 block w-full border-gray-300 rounded">{{ old('notes', $booking->notes) }}</textarea>
    @error('notes')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
  </div>
</div>
