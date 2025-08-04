<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl font-semibold">Generate Slots</h2>
  </x-slot>

  <div class="max-w-xl mx-auto py-6">
    @if(session('success'))
      <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
        {{ session('success') }}
      </div>
    @endif

    @if($errors->any())
      <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
        <ul class="list-disc pl-5">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('admin.generate-slots.store') }}">
      @csrf

      <div class="mb-4">
        <label class="block mb-1">Depot</label>
        <select name="depot_id" class="w-full border rounded p-2">
          @foreach($depots as $depot)
            <option value="{{ $depot->id }}">{{ $depot->name }}</option>
          @endforeach
        </select>
      </div>

      @php
        $tomorrow = \Carbon\Carbon::tomorrow(config('app.timezone'))->format('Y-m-d');
        $selectedDate = old('date', request('date', $tomorrow));
      @endphp

      <div class="mb-4">
        <label class="block mb-1">Date</label>
        <input 
          type="date" 
          name="date" 
          value="{{ $selectedDate }}" 
          min="{{ $tomorrow }}"
          class="w-full border rounded p-2"
        >
      </div>

      <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        Generate Slots
      </button>
    </form>
  </div>
</x-app-layout>
