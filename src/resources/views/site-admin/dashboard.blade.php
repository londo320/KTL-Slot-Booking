<x-site-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                🚪 Gate Operations Dashboard
            </h2>
            <div class="text-sm text-gray-600">
                🕒 Last Updated: {{ now()->format('H:i:s') }} | Auto-refresh: 30s
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Depot Assignment Info -->
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4">
            <div class="flex">
                <div class="ml-3">
                    <p class="text-sm text-green-700">
                        <strong>Your Assigned Gates:</strong>
                        @foreach($userDepots as $depot)
                            <span class="inline-block bg-green-100 px-2 py-1 rounded text-xs ml-1">
                                🚪 {{ $depot->name }}
                            </span>
                        @endforeach
                    </p>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="text-2xl">📋</div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Expected Today
                                </dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ $stats['total_expected'] }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="text-2xl">✅</div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Arrived Today
                                </dt>
                                <dd class="text-lg font-medium text-green-600">
                                    {{ $stats['arrived_today'] }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="text-2xl">🏢</div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    On Site Now
                                </dt>
                                <dd class="text-lg font-medium text-blue-600">
                                    {{ $stats['on_site_now'] }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="text-2xl">🕒</div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Departed Today
                                </dt>
                                <dd class="text-lg font-medium text-gray-600">
                                    {{ $stats['departed_today'] }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="text-2xl">⚠️</div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Overdue
                                </dt>
                                <dd class="text-lg font-medium text-red-600">
                                    {{ $stats['overdue'] }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Search -->
        <div class="mb-8 bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">🔍 Quick Vehicle Search</h3>
            <form method="GET" action="{{ route('site.search') }}" class="flex space-x-4">
                <div class="flex-1">
                    <input type="text" name="search" 
                           placeholder="Search by booking ref, vehicle reg, container, driver name..."
                           class="w-full border-gray-300 rounded-lg">
                </div>
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
                    Search
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- Expected Arrivals Next Hour -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        🚛 Expected Arrivals (Next Hour)
                    </h3>
                    
                    @if($nextHourArrivals->isEmpty())
                        <p class="text-gray-500 text-sm">No expected arrivals in the next hour.</p>
                    @else
                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            @foreach($nextHourArrivals as $booking)
                                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg border-l-4 border-yellow-400">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            <span class="font-mono text-xs bg-blue-100 px-2 py-1 rounded font-semibold">
                                                {{ $booking->booking_reference }}
                                            </span>
                                            <span class="font-medium text-sm">{{ $booking->customer->name ?? 'N/A' }}</span>
                                        </div>
                                        <div class="text-xs text-gray-600 mt-1">
                                            📍 {{ $booking->slot->depot->name }} • 
                                            ⏰ {{ $booking->slot->start_at->format('H:i') }}
                                            @if($booking->vehicle_registration)
                                                • 🚛 {{ $booking->vehicle_registration }}
                                            @endif
                                        </div>
                                        @if($booking->estimated_arrival)
                                            <div class="text-xs text-purple-600 mt-1">
                                                📅 Est: {{ Carbon\Carbon::parse($booking->estimated_arrival)->format('H:i') }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xs font-medium text-orange-600">
                                            {{ $booking->slot->start_at->diffForHumans() }}
                                        </div>
                                        <button onclick="openArrivalModal({{ $booking->id }}, '{{ $booking->booking_reference }}', '{{ addslashes($booking->customer->name ?? 'N/A') }}', '{{ $booking->slot->depot->name }}', '{{ $booking->slot->start_at->format('d-M-Y H:i') }}', '{{ $booking->vehicle_registration ?? '' }}', '{{ $booking->container_number ?? '' }}', '{{ $booking->driver_name ?? '' }}', '{{ $booking->driver_phone ?? '' }}', '{{ $booking->gate_number ?? '' }}', '{{ $booking->bay_number ?? '' }}', '{{ $booking->actual_cases ?? '' }}', '{{ $booking->actual_pallets ?? '' }}', '{{ $booking->expected_cases ?? 0 }}', '{{ $booking->expected_pallets ?? 0 }}', '{{ addslashes($booking->special_instructions ?? '') }}')" 
                                                class="text-xs bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600 mt-1 inline-block cursor-pointer">
                                            🚛 Process Arrival
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Vehicles On Site -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        🏢 Vehicles Currently On Site
                    </h3>
                    
                    @if($onSiteVehicles->isEmpty())
                        <p class="text-gray-500 text-sm">No vehicles currently on site.</p>
                    @else
                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            @foreach($onSiteVehicles as $booking)
                                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border-l-4 border-blue-400">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            <span class="font-mono text-xs bg-blue-100 px-2 py-1 rounded font-semibold">
                                                {{ $booking->booking_reference }}
                                            </span>
                                            <span class="font-medium text-sm">{{ $booking->customer->name ?? 'N/A' }}</span>
                                        </div>
                                        <div class="text-xs text-gray-600 mt-1">
                                            📍 {{ $booking->slot->depot->name }} • 
                                            ✅ Arrived: {{ $booking->arrived_at->format('H:i') }}
                                            @if($booking->vehicle_registration)
                                                • 🚛 {{ $booking->vehicle_registration }}
                                            @endif
                                        </div>
                                        @if($booking->gate_number || $booking->bay_number)
                                            <div class="text-xs text-green-600 mt-1">
                                                @if($booking->gate_number)🚪 Gate {{ $booking->gate_number }} @endif
                                                @if($booking->bay_number)🏗️ Bay {{ $booking->bay_number }}@endif
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xs text-gray-500">
                                            On site: {{ $booking->arrived_at->diffForHumans() }}
                                        </div>
                                        <form method="POST" action="{{ route('site.bookings.departure', $booking) }}" class="mt-1">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="text-xs bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600">
                                                Mark Departed
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Departures -->
        <div class="mt-8 bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    🕒 Recent Departures (Last 2 Hours)
                </h3>
                
                @if($recentDepartures->isEmpty())
                    <p class="text-gray-500 text-sm">No recent departures.</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($recentDepartures as $booking)
                            <div class="p-3 bg-gray-50 rounded-lg border">
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="font-mono text-xs bg-gray-200 px-2 py-1 rounded">
                                        {{ $booking->booking_reference }}
                                    </span>
                                    <span class="text-sm font-medium">{{ $booking->customer->name ?? 'N/A' }}</span>
                                </div>
                                <div class="text-xs text-gray-600">
                                    📍 {{ $booking->slot->depot->name }}<br>
                                    🕒 Departed: {{ $booking->departed_at->format('H:i') }}<br>
                                    ⏱️ Duration: {{ $booking->arrived_at?->diffInMinutes($booking->departed_at) ?? 0 }} mins
                                    @if($booking->vehicle_registration)
                                        <br>🚛 {{ $booking->vehicle_registration }}
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Arrival Modal -->
    <div id="arrivalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Modal Header -->
                <div class="flex justify-between items-center pb-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">🚛 Vehicle Arrival Processing</h3>
                    <button onclick="closeArrivalModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Booking Summary -->
                <div id="bookingSummary" class="mt-4 p-4 bg-green-50 rounded-lg">
                    <!-- Will be populated by JavaScript -->
                </div>

                <!-- Arrival Form -->
                <form id="arrivalForm" method="POST" class="mt-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <!-- Required Vehicle Registration -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Vehicle Registration <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="vehicleRegistration" name="vehicle_registration" required
                                   placeholder="e.g., AB12 CDE"
                                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            <p class="text-xs text-gray-500 mt-1">Required for arrival processing</p>
                        </div>

                        <!-- Container Number -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Container/Trailer Number</label>
                            <input type="text" id="containerNumber" name="container_number"
                                   placeholder="e.g., CONT123456"
                                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        </div>

                        <!-- Driver Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Driver Name</label>
                            <input type="text" id="driverName" name="driver_name"
                                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        </div>

                        <!-- Driver Phone -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Driver Phone</label>
                            <input type="tel" id="driverPhone" name="driver_phone"
                                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        </div>

                        <!-- Gate Number -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Gate Number</label>
                            <input type="text" id="gateNumber" name="gate_number"
                                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        </div>

                        <!-- Bay Number -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bay Number</label>
                            <input type="text" id="bayNumber" name="bay_number"
                                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        </div>

                        <!-- Actual Cases -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Actual Cases</label>
                            <input type="number" id="actualCases" name="actual_cases" min="0"
                                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            <p id="expectedCases" class="text-xs text-gray-500 mt-1">Expected: 0</p>
                        </div>

                        <!-- Actual Pallets -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Actual Pallets</label>
                            <input type="number" id="actualPallets" name="actual_pallets" min="0"
                                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            <p id="expectedPallets" class="text-xs text-gray-500 mt-1">Expected: 0</p>
                        </div>

                    </div>

                    <!-- Special Instructions -->
                    <div id="specialInstructions" class="mt-4 p-4 bg-yellow-50 rounded-lg border border-yellow-200 hidden">
                        <h4 class="font-medium text-yellow-800 mb-2">⚠️ Special Instructions:</h4>
                        <p id="specialInstructionsText" class="text-yellow-700"></p>
                    </div>

                    <!-- Arrival Time Display -->
                    <div class="mt-4 p-4 bg-green-50 rounded-lg border border-green-200">
                        <h4 class="font-medium text-green-800 mb-2">📅 Arrival Time:</h4>
                        <p class="text-green-700 font-semibold" id="arrivalTime">Will be recorded as: [Current Time]</p>
                    </div>

                    <!-- Form Actions -->
                    <div class="mt-6 flex justify-end space-x-4">
                        <button type="button" onclick="closeArrivalModal()" 
                                class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">
                            🚛 Mark Vehicle Arrived
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentBookingId = null;

        function openArrivalModal(bookingId, bookingRef, customer, depot, scheduledTime, vehicleReg, containerNum, driverName, driverPhone, gateNum, bayNum, actualCases, actualPallets, expectedCases, expectedPallets, specialInstructions) {
            currentBookingId = bookingId;
            
            // Update booking summary
            document.getElementById('bookingSummary').innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <strong>Booking:</strong> ${bookingRef}<br>
                        <strong>Customer:</strong> ${customer}
                    </div>
                    <div>
                        <strong>Depot:</strong> ${depot}<br>
                        <strong>Scheduled:</strong> ${scheduledTime}
                    </div>
                    <div>
                        <strong>Expected:</strong> ${expectedCases} cases, ${expectedPallets} pallets
                    </div>
                </div>
            `;

            // Update form action
            document.getElementById('arrivalForm').action = `/site-admin/bookings/${bookingId}/arrival`;

            // Populate form fields
            document.getElementById('vehicleRegistration').value = vehicleReg;
            document.getElementById('containerNumber').value = containerNum;
            document.getElementById('driverName').value = driverName;
            document.getElementById('driverPhone').value = driverPhone;
            document.getElementById('gateNumber').value = gateNum;
            document.getElementById('bayNumber').value = bayNum;
            document.getElementById('actualCases').value = actualCases;
            document.getElementById('actualPallets').value = actualPallets;

            // Update expected quantities display
            document.getElementById('expectedCases').textContent = `Expected: ${expectedCases}`;
            document.getElementById('expectedPallets').textContent = `Expected: ${expectedPallets}`;

            // Show special instructions if any
            if (specialInstructions && specialInstructions.trim() !== '') {
                document.getElementById('specialInstructionsText').textContent = specialInstructions;
                document.getElementById('specialInstructions').classList.remove('hidden');
            } else {
                document.getElementById('specialInstructions').classList.add('hidden');
            }

            // Update arrival time display
            updateArrivalTime();

            // Show modal
            document.getElementById('arrivalModal').classList.remove('hidden');
            
            // Focus on vehicle registration field
            setTimeout(() => {
                document.getElementById('vehicleRegistration').focus();
            }, 100);
        }

        function closeArrivalModal() {
            document.getElementById('arrivalModal').classList.add('hidden');
            currentBookingId = null;
        }

        function updateArrivalTime() {
            const now = new Date();
            const timeString = now.toLocaleString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            document.getElementById('arrivalTime').textContent = `Will be recorded as: ${timeString}`;
        }

        // Close modal when clicking outside
        document.getElementById('arrivalModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeArrivalModal();
            }
        });

        // Update time display every second
        setInterval(() => {
            if (!document.getElementById('arrivalModal').classList.contains('hidden')) {
                updateArrivalTime();
            }
        }, 1000);
    </script>
</x-site-admin-layout>