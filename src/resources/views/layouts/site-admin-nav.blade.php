<nav class="bg-green-800 text-white">
    <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
        <div class="flex space-x-4">
            <a href="{{ route('site.dashboard') }}"
               class="hover:bg-green-700 px-3 py-2 rounded {{ request()->routeIs('site.dashboard') ? 'bg-green-700' : '' }}">
                🚪 Gate Dashboard
            </a>

            <a href="{{ route('admin.bookings.index') }}"
               class="hover:bg-green-700 px-3 py-2 rounded {{ request()->routeIs('admin.bookings.*') ? 'bg-green-700' : '' }}">
                📋 Bookings
            </a>

            <a href="{{ route('site.arrivals.index') }}"
               class="hover:bg-green-700 px-3 py-2 rounded {{ request()->routeIs('site.arrivals.*') ? 'bg-green-700' : '' }}">
                🚛 Live Arrivals
            </a>

            <a href="{{ route('site.departures.index') }}"
               class="hover:bg-green-700 px-3 py-2 rounded {{ request()->routeIs('site.departures.*') ? 'bg-green-700' : '' }}">
                🕒 Departures
            </a>

            <a href="{{ route('site.search') }}"
               class="hover:bg-green-700 px-3 py-2 rounded {{ request()->routeIs('site.search') ? 'bg-green-700' : '' }}">
                🔍 Quick Search
            </a>
        </div>

        <div class="flex items-center space-x-4">
            {{-- Switch Back Button (Testing Only) --}}
            @if(!app()->isProduction() && session('original_admin_id'))
                <form method="POST" action="{{ route('switch-back') }}">
                    @csrf
                    <button type="submit" class="bg-orange-600 hover:bg-orange-700 px-2 py-1 rounded text-xs font-semibold">
                        🔄 Switch Back to Admin
                    </button>
                </form>
            @endif

            <span class="text-sm">
                👤 {{ auth()->user()->name }} 
                <span class="text-green-300">(Gate Operator)</span>
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