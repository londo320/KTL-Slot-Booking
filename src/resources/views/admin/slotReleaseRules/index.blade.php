@extends('layouts.admin')

@section('content')
<div class="py-6 max-w-7xl mx-auto">
  <h1 class="text-2xl font-semibold mb-6">Slot Release Rules</h1>

  @if(session('success'))
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
      {{ session('success') }}
    </div>
  @endif

  @foreach($rules->groupBy('depot.name') as $depotName => $rulesForDepot)
    <div class="mb-8">
      <h2 class="text-xl font-bold mb-2">{{ $depotName }}</h2>
      <table class="w-full bg-white shadow rounded overflow-hidden text-sm mb-4">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-3 py-2 text-left">Customers</th>
            <th class="px-3 py-2 text-left">Day</th>
            <th class="px-3 py-2 text-left">Time</th>
            <th class="px-3 py-2 text-left">Cutoff</th>
            <th class="px-3 py-2 text-left">Priority</th>
            <th class="px-3 py-2 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($rulesForDepot->sortBy('release_day') as $rule)
            <tr class="border-t hover:bg-gray-50">
              <td class="px-3 py-2 align-top">
                @if($rule->customers->count())
                  <div class="flex flex-wrap gap-1">
                    @foreach($rule->customers as $cust)
                      <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">{{ $cust->name }}</span>
                    @endforeach
                  </div>
                @else
                  <span class="text-gray-500">Any</span>
                @endif
              </td>
              <td class="px-3 py-2">{{ ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'][$rule->release_day - 1] }}</td>
              <td class="px-3 py-2">{{ \Carbon\Carbon::createFromFormat('H:i:s', $rule->release_time)->format('H:i') }}</td>
              <td class="px-3 py-2">{{ $rule->lock_cutoff_days }}d @ {{ \Carbon\Carbon::createFromFormat('H:i:s', $rule->lock_cutoff_time)->format('H:i') }}</td>
              <td class="px-3 py-2">{{ $rule->priority }}</td>
              <td class="px-3 py-2 space-x-2">
                <a href="{{ route('admin.slotReleaseRules.edit', $rule) }}"
                   class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-xs">Edit</a>
                <form action="{{ route('admin.slotReleaseRules.destroy', $rule) }}" method="POST" class="inline" onsubmit="return confirm('Delete this rule?');">
                  @csrf @method('DELETE')
                  <button class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs">Delete</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endforeach

  <div class="mt-4">
    {{ $rules->links() }}
  </div>
</div>
@endsection
