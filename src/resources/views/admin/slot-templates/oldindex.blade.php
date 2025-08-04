<x-app-layout>
    @include('layouts.admin-nav')

@section('content')
<div class="p-6 space-y-4">
  <a href="{{ route('admin.slot-templates.create') }}"
     class="bg-blue-600 text-white px-4 py-2 rounded">New Template</a>

  @if(session('success'))
    <div class="bg-green-100 text-green-800 p-2 rounded">{{ session('success') }}</div>
  @endif

  <table class="min-w-full border">
    <thead>
      <tr class="bg-gray-200">
        <th class="p-2">Depot</th>
        <th class="p-2">Weekday</th>
        <th class="p-2">Start</th>
        <th class="p-2">Type</th>
        <th class="p-2">Length</th>
        <th class="p-2">Actions</th>
      </tr>
    </thead>
    <tbody>
    @foreach($templates as $tpl)
      <tr class="border-t">
        <td class="p-2">{{ $tpl->depot->name }}</td>
        <td class="p-2">{{ \Illuminate\Support\Str::ucfirst(
            \Carbon\Carbon::create()->startOfWeek()->addDays($tpl->weekday)->format('l')
          ) }}</td>
        <td class="p-2">{{ \Carbon\Carbon::parse($tpl->start_time)->format('H:i') }}</td>
        <td class="p-2">{{ $tpl->bookingType->name }}</td>
        <td class="p-2">{{ $tpl->default_length }}m</td>
        <td class="p-2 space-x-2">
          <a href="{{ route('admin.slot-templates.edit',$tpl) }}"
             class="text-blue-600">Edit</a>
          <form method="POST" action="{{ route('admin.slot-templates.destroy',$tpl) }}"
                class="inline">@csrf @method('DELETE')
            <button class="text-red-600"
                    onclick="return confirm('Really delete?')">Del</button>
          </form>
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>
</div>
@endsection
</x-app-layout>
