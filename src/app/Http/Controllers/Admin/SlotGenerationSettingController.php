<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Depot;
use App\Models\SlotGenerationSetting;
use Illuminate\Http\Request;

class SlotGenerationSettingController extends Controller
{
    public function index()
    {
        $depots = Depot::with('slotGenerationSetting')->get();
        return view('admin.slot-settings.index', compact('depots'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'settings' => 'array',
            'settings.*.start_time'        => 'required|date_format:H:i',
            'settings.*.end_time'          => 'required|date_format:H:i|after:settings.*.start_time',
            'settings.*.interval_minutes'  => 'required|integer|min:15|max:180',
            'settings.*.slots_per_block'   => 'required|integer|min:1|max:10',
            'settings.*.default_capacity'  => 'required|integer|min:1|max:10',
            'settings.*.days_active'       => 'array',
        ]);

        foreach ($request->settings ?? [] as $depotId => $values) {
            SlotGenerationSetting::updateOrCreate(
                ['depot_id' => $depotId],
                [
                    'start_time'        => $values['start_time'],
                    'end_time'          => $values['end_time'],
                    'interval_minutes'  => $values['interval_minutes'],
                    'slots_per_block'   => $values['slots_per_block'],
                    'default_capacity'  => $values['default_capacity'],
                    'days_active'       => $values['days_active'] ?? [],
                ]
            );
        }



        return redirect()->route('admin.slot-settings.index')->with('success', 'Slot generation settings updated.');
    }
}
