<nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 mb-6">
  <ul class="flex space-x-4 px-6 py-3">
    <li>
      <a href="{{ route('admin.dashboard') }}"
         class="px-3 py-1 rounded {{ request()->routeIs('admin.dashboard') ? 'bg-blue-500 text-white' : 'text-gray-700 dark:text-gray-300' }}">
        Dashboard
      </a>
    </li>
    <li>
      <a href="{{ route('admin.depots.index') }}"
         class="px-3 py-1 rounded {{ request()->routeIs('admin.depots.*') ? 'bg-blue-500 text-white' : 'text-gray-700 dark:text-gray-300' }}">
        Depots
      </a>
    </li>
    <li>
      <a href="{{ route('admin.booking-types.index') }}"
         class="px-3 py-1 rounded {{ request()->routeIs('admin.booking-types.*') ? 'bg-blue-500 text-white' : 'text-gray-700 dark:text-gray-300' }}">
        Booking Types
      </a>
    </li>
    <li>
      <a href="{{ route('admin.slots.index') }}"
         class="px-3 py-1 rounded {{ request()->routeIs('admin.slots.*') ? 'bg-blue-500 text-white' : 'text-gray-700 dark:text-gray-300' }}">
        Slots
      </a>
    </li>
    <li>
      <a href="{{ route('admin.bookings.index') }}"
         class="px-3 py-1 rounded {{ request()->routeIs('admin.bookings.*') ? 'bg-blue-500 text-white' : 'text-gray-700 dark:text-gray-300' }}">
        Bookings
      </a>
    </li>
    <li>
      <a href="{{ route('admin.settings.index') }}"
         class="px-3 py-1 rounded {{ request()->routeIs('admin.settings.*') ? 'bg-blue-500 text-white' : 'text-gray-700 dark:text-gray-300' }}">
        Settings
      </a>
    </li>
    <li>
      <a href="{{ route('admin.slot-templates.index') }}"
         class="px-3 py-1 rounded {{ request()->routeIs('admin.slots-templates.*') ? 'bg-blue-500 text-white' : 'text-gray-700 dark:text-gray-300' }}">
        Slots-Template
      </a>
    </li>
    <li>
      <a href="{{ route('admin.settings.dashboard') }}" class="block px-4 py-2 hover:bg-gray-100">
       🛠 Admin Settings
  </a>
</li>
  </ul>
</nav>
