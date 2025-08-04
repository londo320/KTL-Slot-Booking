<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Depot;
use App\Models\Slot;
use Illuminate\Http\Request;

class SlotCapacityController extends Controller
{
    public function index()
    {
        $depots = Depot::with(['slots' => function ($q) {
            $q->orderBy('start_at');
        }])->get();

        return view('admin.slot-capacity.index', compact('depots'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'capacities' => 'array',
            'capacities.*' => 'nullable|integer|min:1|max:10',
        ]);

        foreach ($request->capacities ?? [] as $slotId => $value) {
            Slot::where('id', $slotId)->update(['capacity' => $value]);
        }

        return redirect()->route('admin.slot-capacity.index')->with('success', 'Capacities updated.');
    }
}
