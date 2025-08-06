<div class="grid grid-cols-2 gap-6">
  {{-- Customer (admin only) --}}
  @if(auth()->user()->hasRole('admin'))
    <div class="col-span-2">
      <label class="block text-sm font-medium">Customer</label>
      <select name="customer_id" required class="mt-1 block w-full border-gray-300 rounded">
        <option value="">– Choose customer –</option>
        @foreach($customers as $customer)
          <option value="{{ $customer->id }}"
            @selected(old('customer_id', $booking->customer_id) == $customer->id)
          >
            {{ $customer->name }}
          </option>
        @endforeach
      </select>
      @error('customer_id')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
    </div>
  @endif

  {{-- Slot --}}
<div class="col-span-2">
  <label class="block text-sm font-medium">Slot 
    <span class="text-xs text-gray-500 ml-2">🌐 = Public, 🔒 = Customer Restricted</span>
  </label>
  <select name="slot_id" required class="mt-1 block w-full border-gray-300 rounded">
    <option value="">– Choose slot –</option>

    @php
      $groupedSlots = $slots->sortBy('start_at')->groupBy(fn($slot) => $slot->depot->name);
    @endphp

    @foreach($groupedSlots as $depotName => $depotSlots)
      <optgroup label="{{ $depotName }}">
        @foreach($depotSlots as $slot)
          @php
            $isRestricted = $slot->allowed_customers->count() > 0;
            $restrictedToCustomers = $isRestricted ? $slot->allowed_customers->pluck('name')->join(', ') : '';
          @endphp
          <option value="{{ $slot->id }}"
            @selected(old('slot_id', $booking->slot_id) == $slot->id)
            title="{{ $isRestricted ? 'Restricted to: ' . $restrictedToCustomers : 'Public slot - available to all customers' }}">
            {{ $isRestricted ? '🔒' : '🌐' }} {{ $slot->start_at->format('D d-M H:i') }} → {{ $slot->end_at->format('H:i') }}
            @if($isRestricted)
              <span class="text-gray-600">({{ $slot->allowed_customers->pluck('name')->take(2)->join(', ') }}{{ $slot->allowed_customers->count() > 2 ? '+' . ($slot->allowed_customers->count() - 2) : '' }})</span>
            @endif
          </option>
        @endforeach
      </optgroup>
    @endforeach
  </select>
  @error('slot_id')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
</div>

  {{-- Booking Type --}}
  <div>
    <label class="block text-sm font-medium">Booking Type</label>
    <select name="booking_type_id" required class="mt-1 block w-full border-gray-300 rounded">
      <option value="">– Choose type –</option>
      @foreach($types as $type)
        <option value="{{ $type->id }}"
          @selected(old('booking_type_id', $booking->booking_type_id) == $type->id)
        >
          {{ $type->name }}
        </option>
      @endforeach
    </select>
    @error('booking_type_id')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
  </div>

{{-- Container Size --}}
<div>
  <label class="block text-sm font-medium">Container Size</label>
  <select name="container_size"
          class="mt-1 block w-full border-gray-300 rounded">
    <option value="">– Select Size –</option>
    <option value="20" {{ old('container_size', $booking->container_size) == 20 ? 'selected' : '' }}>20ft</option>
    <option value="40" {{ old('container_size', $booking->container_size) == 40 ? 'selected' : '' }}>40ft</option>
  </select>
  @error('container_size')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
</div>

  {{-- Reference --}}
  <div>
    <label class="block text-sm font-medium">Reference</label>
    <input type="text" name="reference"
           value="{{ old('reference', $booking->reference) }}"
           class="mt-1 block w-full border-gray-300 rounded">
    @error('reference')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
  </div>


<div class="grid grid-cols-2 gap-6">


  {{-- Expected Cases --}}
  <div>
    <label class="block text-sm font-medium">Expected Cases</label>
    <input type="number" name="expected_cases"
           value="{{ old('expected_cases', $booking->expected_cases) }}"
           class="mt-1 block w-full border-gray-300 rounded">
    @error('expected_cases')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
  </div>

{{-- Actual Cases --}}
<div>
  <label class="block text-sm font-medium">Actual Cases</label>
  <input type="number" name="actual_cases"
         value="{{ old('actual_cases', $booking->actual_cases) }}"
         class="w-full border border-gray-300 rounded px-3 py-2
                {{ !$booking->exists ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : 'bg-white' }}"
         {{ !$booking->exists ? 'readonly disabled' : '' }}>
</div>

  {{-- Expected Pallets --}}
  <div>
    <label class="block text-sm font-medium">Expected Pallets</label>
    <input type="number" name="expected_pallets"
           value="{{ old('expected_pallets', $booking->expected_pallets) }}"
           class="mt-1 block w-full border-gray-300 rounded">
    @error('expected_pallets')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
  </div>

  {{-- Actual Pallets --}}
  <div>
    <label class="block text-sm font-medium">Actual Pallets</label>
    <input type="number" name="actual_pallets"
           value="{{ old('actual_pallets', $booking->actual_pallets) }}"
           class="w-full border border-gray-300 rounded px-3 py-2
                {{ !$booking->exists ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : 'bg-white' }}"
         {{ !$booking->exists ? 'readonly disabled' : '' }}>
  </div>
</div>


  {{-- Notes --}}
  <div class="col-span-2">
    <label class="block text-sm font-medium">Notes</label>
    <textarea name="notes" rows="3"
              class="mt-1 block w-full border-gray-300 rounded">{{ old('notes', $booking->notes) }}</textarea>
    @error('notes')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
  </div>

  {{-- Transportation Details Section --}}
  @if($booking->exists)
    <div class="col-span-2 mt-6 border-t pt-6">
      <h3 class="text-lg font-medium text-gray-900 mb-4">🚛 Transportation Details</h3>
      <div class="grid grid-cols-2 gap-4">
        
        {{-- Vehicle Registration --}}
        <div>
          <label class="block text-sm font-medium text-gray-700">Vehicle Registration</label>
          <input type="text" name="vehicle_registration"
                 value="{{ old('vehicle_registration', $booking->vehicle_registration) }}"
                 placeholder="e.g., AB12 CDE"
                 class="mt-1 block w-full border-gray-300 rounded-lg">
          @error('vehicle_registration')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
        </div>

        {{-- Container Number --}}
        <div>
          <label class="block text-sm font-medium text-gray-700">Container/Trailer Number</label>
          <input type="text" name="container_number"
                 value="{{ old('container_number', $booking->container_number) }}"
                 placeholder="e.g., CONT123456"
                 class="mt-1 block w-full border-gray-300 rounded-lg">
          @error('container_number')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
        </div>

        {{-- Driver Name --}}
        <div>
          <label class="block text-sm font-medium text-gray-700">Driver Name</label>
          <input type="text" name="driver_name"
                 value="{{ old('driver_name', $booking->driver_name) }}"
                 class="mt-1 block w-full border-gray-300 rounded-lg">
          @error('driver_name')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
        </div>

        {{-- Driver Phone --}}
        <div>
          <label class="block text-sm font-medium text-gray-700">Driver Phone</label>
          <input type="tel" name="driver_phone"
                 value="{{ old('driver_phone', $booking->driver_phone) }}"
                 class="mt-1 block w-full border-gray-300 rounded-lg">
          @error('driver_phone')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
        </div>

        {{-- Carrier Company --}}
        <div>
          <label class="block text-sm font-medium text-gray-700">Carrier Company</label>
          <input type="text" name="carrier_company"
                 value="{{ old('carrier_company', $booking->carrier_company) }}"
                 class="mt-1 block w-full border-gray-300 rounded-lg">
          @error('carrier_company')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
        </div>

        {{-- Gate Number --}}
        <div>
          <label class="block text-sm font-medium text-gray-700">Gate Number</label>
          <input type="text" name="gate_number"
                 value="{{ old('gate_number', $booking->gate_number) }}"
                 class="mt-1 block w-full border-gray-300 rounded-lg">
          @error('gate_number')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
        </div>

        {{-- Bay Number --}}
        <div>
          <label class="block text-sm font-medium text-gray-700">Bay Number</label>
          <input type="text" name="bay_number"
                 value="{{ old('bay_number', $booking->bay_number) }}"
                 class="mt-1 block w-full border-gray-300 rounded-lg">
          @error('bay_number')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
        </div>

        {{-- Special Instructions --}}
        <div class="col-span-2">
          <label class="block text-sm font-medium text-gray-700">Special Instructions</label>
          <textarea name="special_instructions" rows="2"
                    class="mt-1 block w-full border-gray-300 rounded-lg">{{ old('special_instructions', $booking->special_instructions) }}</textarea>
          @error('special_instructions')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
        </div>

      </div>

      @if($booking->arrived_at)
        <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
          <p class="text-sm text-green-800">
            ✅ <strong>Vehicle Arrived:</strong> {{ $booking->arrived_at->format('d-M-Y H:i:s') }}
            @if($booking->departed_at)
              <br>🕒 <strong>Departed:</strong> {{ $booking->departed_at->format('d-M-Y H:i:s') }}
            @endif
          </p>
        </div>
      @endif
    </div>
  @endif
