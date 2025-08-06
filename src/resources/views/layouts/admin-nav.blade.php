<nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 mb-6">
  <div class="flex justify-between items-center px-6 py-3">
    <ul class="flex space-x-4">
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

    {{-- User Switching (Testing Only) --}}
    @if(!app()->isProduction() && session('original_admin_id'))
      <div class="flex items-center space-x-2">
        <span class="text-sm text-orange-600 font-medium">🔄 Testing as: {{ auth()->user()->name }}</span>
        <form action="{{ route('switch-back') }}" method="POST" class="inline">
          @csrf
          <button type="submit" class="px-2 py-1 bg-orange-500 text-white rounded text-xs hover:bg-orange-600">
            Switch Back
          </button>
        </form>
      </div>
    @elseif(!app()->isProduction())
      <div class="relative">
        <select onchange="switchUser(this.value)" class="text-xs border border-gray-300 rounded px-2 py-1 bg-white">
          <option value="">🔄 Switch User (Testing)</option>
          @foreach(\App\Models\User::with('roles')->get() as $user)
            <option value="{{ $user->id }}">
              {{ $user->name }} ({{ $user->roles->pluck('name')->join(', ') ?: 'No Role' }})
            </option>
          @endforeach
        </select>
      </div>
    @endif
  </div>

  <script>
  function switchUser(userId) {
    if (userId) {
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = `/admin/switch-user/${userId}`;
      
      const token = document.createElement('input');
      token.type = 'hidden';
      token.name = '_token';
      token.value = '{{ csrf_token() }}';
      
      form.appendChild(token);
      document.body.appendChild(form);
      form.submit();
    }
  }
  </script>
</nav>
