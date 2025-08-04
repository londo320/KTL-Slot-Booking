<x-depot-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
            🚛 Vehicle Arrival - {{ $booking->booking_reference }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            
            <!-- Booking Summary -->
            <div class="px-6 py-4 border-b bg-gray-50">
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

            <form method="POST" action="{{ route('depot.bookings.arrival', $booking) }}" class="p-6">
                @csrf
                
                <h3 class="text-lg font-medium text-gray-900 mb-6">🚛 Vehicle Arrival Details</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Required Vehicle Registration -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Vehicle Registration <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="vehicle_registration" required
                               value="{{ old('vehicle_registration', $booking->vehicle_registration) }}"
                               placeholder="e.g., AB12 CDE"
                               class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        @error('vehicle_registration')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Required for arrival processing</p>
                    </div>

                    <!-- Container/Trailer Number -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Container/Trailer Number
                        </label>
                        <input type="text" name="container_number"
                               value="{{ old('container_number', $booking->container_number) }}"
                               placeholder="e.g., CONT123456 or TR123456"
                               class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        @error('container_number')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Can be updated if different from booking</p>
                    </div>

                    <!-- Driver Details -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Driver Name</label>
                        <input type="text" name="driver_name"
                               value="{{ old('driver_name', $booking->driver_name) }}"
                               class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Driver Phone</label>
                        <input type="tel" name="driver_phone"
                               value="{{ old('driver_phone', $booking->driver_phone) }}"
                               placeholder="e.g., +44 7XXX XXXXXX"
                               class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Gate/Bay Assignment -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gate Number</label>
                        <input type="text" name="gate_number"
                               value="{{ old('gate_number', $booking->gate_number) }}"
                               placeholder="e.g., Gate 1"
                               class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bay Number</label>
                        <input type="text" name="bay_number"
                               value="{{ old('bay_number', $booking->bay_number) }}"
                               placeholder="e.g., Bay A1"
                               class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Actual Quantities -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Actual Cases</label>
                        <input type="number" name="actual_cases" min="0"
                               value="{{ old('actual_cases', $booking->actual_cases) }}"
                               class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="text-xs text-gray-500 mt-1">Expected: {{ $booking->expected_cases ?? 0 }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Actual Pallets</label>
                        <input type="number" name="actual_pallets" min="0"
                               value="{{ old('actual_pallets', $booking->actual_pallets) }}"
                               class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="text-xs text-gray-500 mt-1">Expected: {{ $booking->expected_pallets ?? 0 }}</p>
                    </div>

                </div>

                @if($booking->special_instructions)
                    <div class="mt-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                        <h4 class="font-medium text-yellow-800 mb-2">⚠️ Special Instructions:</h4>
                        <p class="text-yellow-700">{{ $booking->special_instructions }}</p>
                    </div>
                @endif

                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('depot.bookings.index') }}" 
                       class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold">
                        🚛 Mark Vehicle Arrived
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-depot-admin-layout>