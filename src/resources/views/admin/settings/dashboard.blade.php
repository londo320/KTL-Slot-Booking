<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl font-semibold">🛠 Admin Settings Panel</h2>
  </x-slot>

  <div class="max-w-4xl mx-auto py-6 space-y-4">
    <div class="grid grid-cols-2 gap-4">
      <a href="{{ route('admin.depots.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        📦 Manage Depots
      </a>

      <a href="{{ route('admin.booking-types.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        🧱 Manage Booking Types
      </a>

      <a href="{{ route('admin.slot-templates.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        🕒 Slot Duration Rules (Handball etc.)
      </a>

      <a href="{{ route('admin.slot-capacity.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        ⚙️ Slot Generation Rules
      </a>

      <a href="{{ route('admin.slots.generate.form') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        🧮 Generate Slots
      </a>

      <a href="{{ route('admin.slot-usage.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        📊 Slot Usage Viewer
      </a>

      <a href="{{ route('admin.slot-capacity.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        🗳️ Slot Capacity
      </a>

      <a href="{{ route('admin.products.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        🗳️ Products
      </a>
      
      <a href="{{ route('admin.users.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        👥 Users Settings
      </a>
      
      <a href="{{ route('admin.customers.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        👥 Customer Settings
      </a>

      <a href="{{ route('admin.slotReleaseRules.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        ⚙️ Slot Rules Config
      </a>

@if($depots->count())
  <div class="col-span-2 mt-6">
    <h3 class="text-lg font-semibold mb-2">🔁 Customer Depot Product Rules</h3>
    <p class="text-sm text-gray-600 mb-2">
      <a href="{{ route('admin.customer-depot-products.index') }}" class="text-blue-600 hover:underline">
        Manage Customer-Depot-Product relationships
      </a>
    </p>
  </div>
@endif

    </div>
  </div>
</x-app-layout>
