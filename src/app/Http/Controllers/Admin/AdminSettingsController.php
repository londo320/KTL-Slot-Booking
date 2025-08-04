<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Depot; // ✅ Add this
use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    public function dashboard()
    {
        $depots = Depot::orderBy('name')->get();
        return view('admin.settings.dashboard', compact('depots'));
    }
}