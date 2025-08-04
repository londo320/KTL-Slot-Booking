<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Depot;

class SettingsController extends Controller
{
    public function __construct()
    {
        // Apply the same auth + admin check you use elsewhere
       $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Show the settings form.
     */
    public function index()
    {
        // For example, you might want to let the admin pick a "default depot"
        $depots = Depot::all();
        return view('admin.settings', compact('depots'));
    }

    /**
     * Handle form submission.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'depot_id' => 'required|exists:depots,id',
        ]);

        // TODO: persist your settings somewhere (e.g. in the DB, a config file, etc.)
        // Here's just a flash-and-redirect stub:
        session()->flash('success', 'Settings saved successfully.');

        return redirect()->route('admin.settings.index');
    }
}
