<nav class="bg-indigo-800 text-white">
    <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
        <div class="flex space-x-4">
            <a href="{{ route('depot.dashboard') }}"
               class="hover:bg-indigo-700 px-3 py-2 rounded {{ request()->routeIs('depot.dashboard') ? 'bg-indigo-700' : '' }}">
                🏢 Dashboard
            </a>

            <a href="{{ route('depot.bookings.index') }}"
               class="hover:bg-indigo-700 px-3 py-2 rounded {{ request()->routeIs('depot.bookings.*') ? 'bg-indigo-700' : '' }}">
                📋 Bookings
            </a>

            <a href="{{ route('depot.slots.index') }}"
               class="hover:bg-indigo-700 px-3 py-2 rounded {{ request()->routeIs('depot.slots.*') ? 'bg-indigo-700' : '' }}">
                ⏰ Slots
            </a>

            <a href="{{ route('depot.arrivals.index') }}"
               class="hover:bg-indigo-700 px-3 py-2 rounded {{ request()->routeIs('depot.arrivals.*') ? 'bg-indigo-700' : '' }}">
                🚛 Live Arrivals
            </a>
        </div>

        <div class="flex items-center space-x-4">
            <span class="text-sm">
                👤 {{ auth()->user()->name }} 
                <span class="text-indigo-300">(Depot Admin)</span>
            </span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="bg-red-600 hover:bg-red-700 px-3 py-2 rounded text-sm">
                    Logout
                </button>
            </form>
        </div>
    </div>
</nav>