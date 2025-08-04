<?php

namespace App\Http\Controllers\Admin;

use App\Models\SlotReleaseRule;
use App\Models\Depot;
use App\Models\Customer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SlotReleaseRuleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:admin']);
    }

    public function index()
    {
        $rules = SlotReleaseRule::with(['depot','customers'])
            ->orderBy('depot_id')
            ->paginate(15);

        $depots    = Depot::orderBy('name')->pluck('name','id');
        $customers = Customer::orderBy('name')->pluck('name','id');

        return view('admin.slotReleaseRules.index', compact('rules','depots','customers'));
    }

    public function create()
    {
        $rule      = new SlotReleaseRule;
        $depots    = Depot::orderBy('name')->pluck('name','id');
        $customers = Customer::orderBy('name')->pluck('name','id');

        return view('admin.slotReleaseRules.form', compact('rule','depots','customers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'depot_id'         => 'required|exists:depots,id',
            'release_day'      => 'required|integer|min:1|max:7',
            'release_time'     => 'required|date_format:H:i',
            'lock_cutoff_days' => 'required|integer|min:0',
            'lock_cutoff_time' => 'required|date_format:H:i',
            'priority'         => 'required|integer|min:0',
            'customer_ids'     => 'nullable|array',
            'customer_ids.*'   => 'exists:customers,id',
        ]);

        $rule = SlotReleaseRule::create($data);

        // sync many-to-many
        $rule->customers()->sync($request->input('customer_ids', []));

        return redirect()->route('admin.slotReleaseRules.index')
                         ->with('success', 'Rule created successfully.');
    }

    public function edit(SlotReleaseRule $rule)
    {
        $depots    = Depot::orderBy('name')->pluck('name','id');
        $customers = Customer::orderBy('name')->pluck('name','id');

        // eager load customers relationship
        $rule->load('customers');

        return view('admin.slotReleaseRules.form', compact('rule','depots','customers'));
    }

    public function update(Request $request, SlotReleaseRule $rule)
    {
        $data = $request->validate([
            'depot_id'         => 'required|exists:depots,id',
            'release_day'      => 'required|integer|min:1|max:7',
            'release_time'     => 'required|date_format:H:i',
            'lock_cutoff_days' => 'required|integer|min:0',
            'lock_cutoff_time' => 'required|date_format:H:i',
            'priority'         => 'required|integer|min:0',
            'customer_ids'     => 'nullable|array',
            'customer_ids.*'   => 'exists:customers,id',
        ]);

        $rule->update($data);

        // sync many-to-many
        $rule->customers()->sync($request->input('customer_ids', []));

        return redirect()->route('admin.slotReleaseRules.index')
                         ->with('success', 'Rule updated successfully.');
    }

    public function destroy(SlotReleaseRule $rule)
    {
        $rule->delete();

        return redirect()->route('admin.slotReleaseRules.index')
                         ->with('success', 'Rule deleted successfully.');
    }
}
