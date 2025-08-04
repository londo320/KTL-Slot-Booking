<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SlotTemplate;
use App\Models\Depot;
use App\Models\BookingType;

class SlotTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }


public function index()
{
    $depots = Depot::all();
    $templates = SlotTemplate::with('depot')
        ->orderBy('depot_id')
        ->orderBy('day_of_week')
        ->orderBy('start_time')
        ->get()
        ->groupBy('depot.name'); // Group by depot name for easier display

    return view('admin.slot-templates.index', compact('depots', 'templates'));
}

public function store(Request $request)
{
    $data = $request->validate([
        'depot_id'     => 'required|exists:depots,id',
        'day_of_week'  => 'required|integer|between:0,6',
        'start_time'   => 'required|date_format:H:i',
        'end_time'     => 'required|date_format:H:i',
    ]);

    $start = \Carbon\Carbon::createFromFormat('H:i', $data['start_time']);
    $end   = \Carbon\Carbon::createFromFormat('H:i', $data['end_time']);

    if ($start->eq($end)) {
    return back()->withErrors(['end_time' => 'Start and end time cannot be the same.'])->withInput();
}

if ($end->lessThan($start)) {
    // Handle overnight slot — only allow max 12-hour difference
    $end->addDay();
}

    // Now calculate duration
    $duration = $end->diffInMinutes($start);

    // Validate duration
if ($duration > 720 || $duration % 15 !== 0) {
    return back()->withErrors([
        'end_time' => 'Duration must be in 15-minute intervals and under 12 hours.',
    ])->withInput();
    }

    $data['duration_minutes'] = $duration;

    SlotTemplate::create($data);

    return back()->with('success', 'Template added.');
}

public function edit(SlotTemplate $slotTemplate)
{
    $depots = Depot::all();
    return view('admin.slot-templates.edit', compact('slotTemplate', 'depots'));
}

public function update(Request $request, SlotTemplate $slotTemplate)
{
    $data = $request->validate([
        'depot_id'     => 'required|exists:depots,id',
        'day_of_week'  => 'required|integer|between:0,6',
        'start_time'   => 'required|date_format:H:i',
        'end_time'     => 'required|date_format:H:i',
    ]);

    $start = \Carbon\Carbon::createFromFormat('H:i', $data['start_time']);
    $end   = \Carbon\Carbon::createFromFormat('H:i', $data['end_time']);

    if ($end->lessThanOrEqualTo($start)) {
        $end->addDay();
    }

    $duration = $end->diffInMinutes($start);

    if ($duration % 15 !== 0 || $duration > 720) {
        return back()->withErrors([
            'end_time' => 'Duration must be a multiple of 15 minutes and not exceed 12 hours.',
        ])->withInput();
    }

    $data['duration_minutes'] = $duration;
    $slotTemplate->update($data);

    return redirect()->route('admin.slot-templates.index')->with('success', 'Template updated.');
}

public function destroy(SlotTemplate $slotTemplate)
{
    $slotTemplate->delete();
    return back()->with('success', 'Template deleted.');
}

public function duplicate(Request $request, SlotTemplate $slotTemplate)
{
    $data = $request->validate([
        'depot_ids' => 'required|array|min:1',
        'depot_ids.*' => 'exists:depots,id'
    ]);

    $copied = 0;
    foreach ($data['depot_ids'] as $depotId) {
        if ($depotId == $slotTemplate->depot_id) {
            continue; // Skip original depot
        }

        // Check if template already exists for this depot
        $exists = SlotTemplate::where('depot_id', $depotId)
            ->where('day_of_week', $slotTemplate->day_of_week)
            ->where('start_time', $slotTemplate->start_time)
            ->where('end_time', $slotTemplate->end_time)
            ->exists();

        if (!$exists) {
            SlotTemplate::create([
                'depot_id' => $depotId,
                'day_of_week' => $slotTemplate->day_of_week,
                'start_time' => $slotTemplate->start_time,
                'end_time' => $slotTemplate->end_time,
                'duration_minutes' => $slotTemplate->duration_minutes,
            ]);
            $copied++;
        }
    }

    return back()->with('success', "Template copied to {$copied} depot(s).");
}

public function bulkDuplicate(Request $request)
{
    $data = $request->validate([
        'template_ids' => 'required|array|min:1',
        'template_ids.*' => 'exists:slot_templates,id',
        'depot_ids' => 'nullable|array',
        'depot_ids.*' => 'exists:depots,id',
        'day_of_week' => 'nullable|array',
        'day_of_week.*' => 'integer|between:0,6',
        'copy_type' => 'required|in:depots,days,both'
    ]);

    $templates = SlotTemplate::whereIn('id', $data['template_ids'])->get();
    $totalCopied = 0;

    foreach ($templates as $template) {
        // Copy to other depots (existing functionality)
        if (in_array($data['copy_type'], ['depots', 'both']) && !empty($data['depot_ids'])) {
            foreach ($data['depot_ids'] as $depotId) {
                if ($depotId == $template->depot_id) {
                    continue; // Skip original depot
                }

                $exists = SlotTemplate::where('depot_id', $depotId)
                    ->where('day_of_week', $template->day_of_week)
                    ->where('start_time', $template->start_time)
                    ->where('end_time', $template->end_time)
                    ->exists();

                if (!$exists) {
                    SlotTemplate::create([
                        'depot_id' => $depotId,
                        'day_of_week' => $template->day_of_week,
                        'start_time' => $template->start_time,
                        'end_time' => $template->end_time,
                        'duration_minutes' => $template->duration_minutes,
                    ]);
                    $totalCopied++;
                }
            }
        }

        // Copy to other days of week (new functionality)
        if (in_array($data['copy_type'], ['days', 'both']) && !empty($data['day_of_week'])) {
            foreach ($data['day_of_week'] as $dayOfWeek) {
                if ($dayOfWeek == $template->day_of_week) {
                    continue; // Skip original day
                }

                $exists = SlotTemplate::where('depot_id', $template->depot_id)
                    ->where('day_of_week', $dayOfWeek)
                    ->where('start_time', $template->start_time)
                    ->where('end_time', $template->end_time)
                    ->exists();

                if (!$exists) {
                    SlotTemplate::create([
                        'depot_id' => $template->depot_id,
                        'day_of_week' => $dayOfWeek,
                        'start_time' => $template->start_time,
                        'end_time' => $template->end_time,
                        'duration_minutes' => $template->duration_minutes,
                    ]);
                    $totalCopied++;
                }
            }
        }
    }

    $templateCount = count($data['template_ids']);
    $message = "Copied {$templateCount} template(s). Total new templates created: {$totalCopied}";
    
    return back()->with('success', $message);
}


}