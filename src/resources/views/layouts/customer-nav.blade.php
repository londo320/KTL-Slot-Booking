<nav class="bg-gray-800 text-white">
    <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
        <div class="flex space-x-4">
            <a href="{{ route('customer.dashboard') }}"
               class="hover:bg-gray-700 px-3 py-2 rounded {{ request()->routeIs('customer.dashboard') ? 'bg-gray-700' : '' }}">
                Dashboard
            </a>

            <a href="{{ route('customer.bookings.index') }}"
               class="hover:bg-gray-700 px-3 py-2 rounded {{ request()->routeIs('customer.bookings.index') ? 'bg-gray-700' : '' }}">
                My Bookings
            </a>

            <a href="{{ route('customer.bookings.create') }}"
               class="hover:bg-gray-700 px-3 py-2 rounded {{ request()->routeIs('customer.bookings.create') ? 'bg-gray-700' : '' }}">
                Book a Slot
            </a>
        </div>

        <div class="flex space-x-2">
            {{-- Switch Back Button (Testing Only) --}}
            @if(!app()->isProduction() && session('original_admin_id'))
                <form method="POST" action="{{ route('switch-back') }}">
                    @csrf
                    <button type="submit" class="bg-orange-600 hover:bg-orange-700 px-3 py-2 rounded text-sm font-semibold">
                        🔄 Switch Back to Admin
                    </button>
                </form>
            @endif

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="bg-red-600 hover:bg-red-700 px-3 py-2 rounded text-sm">
                    Logout
                </button>
            </form>
        </div>
    </div>
</nav>
