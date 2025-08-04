<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Depot;
use App\Models\SlotTemplate;
use App\Models\Slot;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class SlotGeneratorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    // Show the slot generator form with depot selector
    public function index()
    {
        $depots = Depot::orderBy('name')->get();
        return view('admin.slots.generate', compact('depots'));
    }

    // Generate slots based on templates and manual days-ahead
public function store(Request $request)
{
    $data = $request->validate([
        'depot_id' => 'required|exists:depots,id',
        'days'     => 'nullable|integer|min:1',
    ]);

    $daysAhead = (int) ($data['days'] ?? config('slots.default_generate_days', 14));
    $now       = Carbon::now();
    $startDate = $now->copy()->addDay()->startOfDay();
    $endDate   = $startDate->copy()->addDays($daysAhead - 1);

    $templates = SlotTemplate::where('depot_id', $data['depot_id'])->get();

    foreach ($templates as $template) {
        $period = CarbonPeriod::create($startDate, '1 day', $endDate);

        foreach ($period as $date) {
            if ($date->dayOfWeek !== (int)$template->day_of_week) {
                continue;
            }

            $dayStart = Carbon::parse("{$date->toDateString()} {$template->start_time}");
            if ($dayStart->lt($now)) {
                continue;
            }

            $rawEnd = Carbon::parse("{$date->toDateString()} {$template->end_time}");
            $dayEnd = $rawEnd->lte($dayStart) ? $rawEnd->addDay() : $rawEnd;

            $slot = Slot::firstOrCreate(
                [
                    'depot_id' => $data['depot_id'],
                    'start_at' => $dayStart,
                ],
                [
                    'end_at'   => $dayEnd,
                    'capacity' => $template->capacity ?: config('slots.default_capacity', 1),
                ]
            );

            // Apply Slot Release Rule logic
            $rule = \App\Models\SlotReleaseRule::with('customers')
                ->where('depot_id', $template->depot_id)
                ->orderByDesc('priority')
                ->first();

            if ($rule) {
                if ($rule->customers->isNotEmpty()) {
                    $slot->allowed_customers()->syncWithoutDetaching(
                        $rule->customers->pluck('id')->toArray()
                    );
                } else {
                    $slot->released_at = now(); // Public slot if no restriction
                    $slot->save();
                }
            } else {
                $slot->released_at = now(); // No rule at all = public slot
                $slot->save();
            }
        }
    }

    return redirect()
        ->route('admin.slots.index')
        ->with('success', "Generated slots from {$startDate->toDateString()} to {$endDate->toDateString()}");
}
}