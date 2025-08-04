<x-depot-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            🏢 Depot Admin Dashboard
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Depot Assignment Info -->
        <div class="mb-6 bg-blue-50 border-l-4 border-blue-400 p-4">
            <div class="flex">
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        <strong>Your Assigned Depots:</strong>
                        @foreach($userDepots as $depot)
                            <span class="inline-block bg-blue-100 px-2 py-1 rounded text-xs ml-1">
                                {{ $depot->name }}
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
                                    Today's Bookings
                                </dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ $stats['total_bookings'] }}
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
                                    Arrived
                                </dt>
                                <dd class="text-lg font-medium text-green-600">
                                    {{ $stats['arrived'] }}
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
                                    On Site
                                </dt>
                                <dd class="text-lg font-medium text-blue-600">
                                    {{ $stats['on_site'] }}
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
                                    Departed
                                </dt>
                                <dd class="text-lg font-medium text-gray-600">
                                    {{ $stats['departed'] }}
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
                            <div class="text-2xl">⏳</div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Outstanding
                                </dt>
                                <dd class="text-lg font-medium text-orange-600">
                                    {{ $stats['outstanding'] }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- Upcoming Bookings -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        🚛 Upcoming Arrivals (Next 2 Hours)
                    </h3>
                    
                    @if($upcomingBookings->isEmpty())
                        <p class="text-gray-500 text-sm">No upcoming bookings in the next 2 hours.</p>
                    @else
                        <div class="space-y-3">
                            @foreach($upcomingBookings as $booking)
                                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            <span class="font-mono text-xs bg-blue-100 px-2 py-1 rounded">
                                                {{ $booking->booking_reference }}
                                            </span>
                                            <span class="font-medium">{{ $booking->customer->name ?? 'N/A' }}</span>
                                        </div>
                                        <div class="text-sm text-gray-600 mt-1">
                                            📍 {{ $booking->slot->depot->name }} • 
                                            ⏰ {{ $booking->slot->start_at->format('H:i') }}
                                            @if($booking->vehicle_registration)
                                                • 🚛 {{ $booking->vehicle_registration }}
                                            @endif
                                            @if($booking->container_number)
                                                • 📦 {{ $booking->container_number }}
                                            @endif
                                        </div>
                                        @if($booking->special_instructions)
                                            <div class="text-xs text-orange-600 mt-1">
                                                ⚠️ {{ Str::limit($booking->special_instructions, 50) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-medium">
                                            {{ $booking->slot->start_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Current Arrivals -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        🏢 Currently On Site
                    </h3>
                    
                    @if($currentArrivals->isEmpty())
                        <p class="text-gray-500 text-sm">No vehicles currently on site.</p>
                    @else
                        <div class="space-y-3">
                            @foreach($currentArrivals as $booking)
                                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            <span class="font-mono text-xs bg-blue-100 px-2 py-1 rounded">
                                                {{ $booking->booking_reference }}
                                            </span>
                                            <span class="font-medium">{{ $booking->customer->name ?? 'N/A' }}</span>
                                        </div>
                                        <div class="text-sm text-gray-600 mt-1">
                                            📍 {{ $booking->slot->depot->name }} • 
                                            ✅ Arrived: {{ $booking->arrived_at->format('H:i') }}
                                            @if($booking->vehicle_registration)
                                                • 🚛 {{ $booking->vehicle_registration }}
                                            @endif
                                            @if($booking->container_number)
                                                • 📦 {{ $booking->container_number }}
                                            @endif
                                        </div>
                                        @if($booking->gate_number || $booking->bay_number)
                                            <div class="text-xs text-blue-600 mt-1">
                                                @if($booking->gate_number)🚪 Gate {{ $booking->gate_number }} @endif
                                                @if($booking->bay_number)🏗️ Bay {{ $booking->bay_number }}@endif
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xs text-gray-500">
                                            On site: {{ $booking->arrived_at->diffForHumans() }}
                                        </div>
                                        <a href="{{ route('depot.bookings.edit', $booking) }}" 
                                           class="text-xs bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600">
                                            Mark Departure
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    ⚡ Quick Actions
                </h3>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('depot.bookings.index') }}" 
                       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        📋 View All Bookings
                    </a>
                    <a href="{{ route('depot.slots.index') }}" 
                       class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        ⏰ Manage Slots
                    </a>
                    <a href="{{ route('depot.arrivals.index') }}" 
                       class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                        🚛 Live Arrivals
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-depot-admin-layout>