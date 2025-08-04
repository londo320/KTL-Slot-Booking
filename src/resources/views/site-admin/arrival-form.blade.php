<x-site-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
            🚛 Gate Arrival Processing - {{ $booking->booking_reference }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            
            <!-- Booking Summary -->
            <div class="px-6 py-4 border-b bg-green-50">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <strong>Customer:</strong> {{ $booking->customer->name ?? 'N/A' }}<br>
                        <strong>Booking Type:</strong> {{ $booking->bookingType->name ?? 'N/A' }}
                    </div>
                    <div>
                        <strong>Depot:</strong> {{ $booking->slot->depot->name }}<br>
                        <strong>Scheduled:</strong> {{ $booking->slot->start_at->format('d-M-Y H:i') }}
                    </div>
                    <div>
                        <strong>Expected:</strong> {{ $booking->expected_cases ?? 0 }} cases, {{ $booking->expected_pallets ?? 0 }} pallets<br>
                        @if($booking->estimated_arrival)
                            <strong>Est. Arrival:</strong> {{ $booking->estimated_arrival->format('d-M-Y H:i') }}
                        @endif
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('site.bookings.arrival', $booking) }}" class="p-6">
                @csrf
                
                <h3 class="text-lg font-medium text-gray-900 mb-6">🚪 Gate Processing - Vehicle Arrival</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Required Vehicle Registration -->
                    <div class="md:col-span-2">
                        <label class="block text-lg font-medium text-gray-700 mb-2">
                            Vehicle Registration <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="vehicle_registration" required
                               value="{{ old('vehicle_registration', $booking->vehicle_registration) }}"
                               placeholder="e.g., AB12 CDE"
                               class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 text-lg p-3">
                        @error('vehicle_registration')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-gray-500 mt-1"><strong>REQUIRED:</strong> Must be entered to process arrival</p>
                    </div>

                    <!-- Container/Trailer Number -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Container/Trailer Number
                        </label>
                        <input type="text" name="container_number"
                               value="{{ old('container_number', $booking->container_number) }}"
                               placeholder="e.g., CONT123456 or TR123456"
                               class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        @error('container_number')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Update if different from booking</p>
                    </div>

                    <!-- Driver Details -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Driver Name</label>
                        <input type="text" name="driver_name"
                               value="{{ old('driver_name', $booking->driver_name) }}"
                               class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                    </div>

                    <!-- Gate Assignment -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gate Number</label>
                        <select name="gate_number" class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            <option value="">Select Gate</option>
                            <option value="Gate 1" @selected(old('gate_number', $booking->gate_number) == 'Gate 1')>Gate 1</option>
                            <option value="Gate 2" @selected(old('gate_number', $booking->gate_number) == 'Gate 2')>Gate 2</option>
                            <option value="Gate 3" @selected(old('gate_number', $booking->gate_number) == 'Gate 3')>Gate 3</option>
                            <option value="Gate 4" @selected(old('gate_number', $booking->gate_number) == 'Gate 4')>Gate 4</option>
                        </select>
                    </div>

                    <!-- Bay Assignment -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bay Number</label>
                        <select name="bay_number" class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            <option value="">Select Bay</option>
                            <option value="Bay A1" @selected(old('bay_number', $booking->bay_number) == 'Bay A1')>Bay A1</option>
                            <option value="Bay A2" @selected(old('bay_number', $booking->bay_number) == 'Bay A2')>Bay A2</option>
                            <option value="Bay B1" @selected(old('bay_number', $booking->bay_number) == 'Bay B1')>Bay B1</option>
                            <option value="Bay B2" @selected(old('bay_number', $booking->bay_number) == 'Bay B2')>Bay B2</option>
                            <option value="Bay C1" @selected(old('bay_number', $booking->bay_number) == 'Bay C1')>Bay C1</option>
                            <option value="Bay C2" @selected(old('bay_number', $booking->bay_number) == 'Bay C2')>Bay C2</option>
                        </select>
                    </div>

                </div>

                @if($booking->special_instructions)
                    <div class="mt-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                        <h4 class="font-medium text-yellow-800 mb-2">⚠️ Special Instructions:</h4>
                        <p class="text-yellow-700 text-lg">{{ $booking->special_instructions }}</p>
                    </div>
                @endif

                @if($booking->hazmat)
                    <div class="mt-6 p-4 bg-red-50 rounded-lg border border-red-200">
                        <h4 class="font-medium text-red-800 mb-2">⚠️ HAZMAT LOAD - Special Handling Required</h4>
                        <p class="text-red-700">This vehicle is carrying hazardous materials. Follow all safety protocols.</p>
                    </div>
                @endif

                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('site.arrivals.index') }}" 
                       class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-8 py-4 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold text-lg">
                        🚪 GATE: MARK ARRIVED
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-site-admin-layout>