@extends('layouts.admin')
@section('content')
<div class="p-6 bg-white rounded shadow">
  <h2 class="text-xl mb-4">Case Ranges for {{ $depot->name }}</h2>
  <a href="{{ route('admin.depots.case-ranges.create', $depot) }}" class="btn">Add Range</a>
  <table class="mt-4 w-full">
    <thead><tr><th>Min</t h><th>Max</th><th>Duration (min)</th><th>Actions</th></tr></thead>
    <tbody>
      @foreach($ranges as $r)
      <tr>
        <td>{{ $r->min_cases ?? '0' }}</td>
        <td>{{ $r->max_cases ?? '∞' }}</td>
        <td>{{ $r->duration_minutes }}</td>
        <td>
          <a href="{{ route('admin.depots.case-ranges.edit', [$depot,$r]) }}" class="link">Edit</a>
          <form action="{{ route('admin.depots.case-ranges.destroy', [$depot,$r]) }}" method="POST" class="inline">
            @csrf @method('DELETE')
            <button onclick="return confirm('Delete?')" class="link text-red-600">Delete</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection