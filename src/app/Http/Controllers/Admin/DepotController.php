<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Depot;
use Illuminate\Http\Request;

class DepotController extends Controller
{
    /**
     * Apply auth + admin middleware to every action.
     */
    public function __construct()
    {
       $this->middleware(['auth', 'role:admin']);
    }

    /**
     * List all depots (paginated).
     */
    public function index()
    {
        $depots = Depot::orderBy('name')->paginate(20);

        return view('admin.depots.index', compact('depots'));
    }

    /**
     * Show the “create depot” form.
     */
    public function create()
    {
        return view('admin.depots.create');
    }

    /**
     * Persist a newly-created depot.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'cut_off_time' => 'required|date_format:H:i'
        ]);

        Depot::create($data);

        return redirect()
            ->route('admin.depots.index')
            ->with('success', 'Depot created successfully.');
    }

    /**
     * Show the “edit depot” form.
     */
    public function edit(Depot $depot)
    {
        return view('admin.depots.edit', compact('depot'));
    }

    /**
     * Update an existing depot.
     */
    public function update(Request $request, Depot $depot)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'cut_off_time' => 'required|date_format:H:i',
        ]);

        $depot->update($data);

        return redirect()
            ->route('admin.depots.index')
            ->with('success', 'Depot updated successfully.');
    }

    /**
     * Delete a depot.
     */
    public function destroy(Depot $depot)
    {
        $depot->delete();

        return redirect()
            ->route('admin.depots.index')
            ->with('success', 'Depot deleted.');
    }
}
