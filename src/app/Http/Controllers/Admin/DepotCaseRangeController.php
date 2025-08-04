<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Depot;
use App\Models\DepotCaseRange;
use Illuminate\Http\Request;

class DepotCaseRangeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:admin']);
    }

    public function index(Depot $depot)
    {
        $ranges = $depot->caseRanges()->orderBy('min_cases')->get();
        return view('admin.depot_case_ranges.index', compact('depot','ranges'));
    }

    public function create(Depot $depot)
    {
        return view('admin.depot_case_ranges.create', compact('depot'));
    }

    public function store(Request $request, Depot $depot)
    {
        $data = $request->validate([
            'min_cases'        => 'nullable|integer|min:0',
            'max_cases'        => 'nullable|integer|min:0',
            'duration_minutes' => 'required|integer|min:1',
        ]);
        $data['depot_id'] = $depot->id;
        DepotCaseRange::create($data);

        return redirect()->route('admin.depots.case-ranges.index', $depot)
                         ->with('success','Range added');
    }

    public function edit(Depot $depot, DepotCaseRange $caseRange)
    {
        return view('admin.depot_case_ranges.edit', compact('depot','caseRange'));
    }

    public function update(Request $request, Depot $depot, DepotCaseRange $caseRange)
    {
        $data = $request->validate([
            'min_cases'        => 'nullable|integer|min:0',
            'max_cases'        => 'nullable|integer|min:0',
            'duration_minutes' => 'required|integer|min:1',
        ]);
        $caseRange->update($data);

        return redirect()->route('admin.depots.case-ranges.index', $depot)
                         ->with('success','Range updated');
    }

    public function destroy(Depot $depot, DepotCaseRange $caseRange)
    {
        $caseRange->delete();
        return back()->with('success','Range deleted');
    }
}