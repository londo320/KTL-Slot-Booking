<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UserSwitchController extends Controller
{
    public function __construct()
    {
        // Only allow in non-production environments
        if (app()->isProduction()) {
            abort(404);
        }
        
        // Apply auth middleware to all methods
        $this->middleware('auth');
        
        // Apply admin role only to switchTo method
        $this->middleware('role:admin')->only('switchTo');
    }

    /**
     * Switch to another user for testing
     */
    public function switchTo(Request $request, User $user)
    {
        $originalUser = Auth::user();
        
        // Store the original admin user ID in session
        if (!Session::has('original_admin_id')) {
            Session::put('original_admin_id', $originalUser->id);
            Session::put('switched_at', now());
        }
        
        // Switch to the target user
        Auth::login($user);
        
        // Determine redirect based on user role
        $redirectRoute = $this->getRedirectRoute($user);
        
        return redirect()->route($redirectRoute)
            ->with('success', "🔄 Switched to user: {$user->name} ({$user->email})");
    }

    /**
     * Switch back to original admin user
     */
    public function switchBack()
    {
        $originalAdminId = Session::get('original_admin_id');
        
        if (!$originalAdminId) {
            return redirect()->route('login')
                ->withErrors(['error' => 'No original admin session found.']);
        }
        
        $originalAdmin = User::find($originalAdminId);
        
        if (!$originalAdmin) {
            return redirect()->route('login')
                ->withErrors(['error' => 'Original admin user not found.']);
        }
        
        // Clear switching session data
        Session::forget(['original_admin_id', 'switched_at']);
        
        // Switch back to original admin
        Auth::login($originalAdmin);
        
        return redirect()->route('admin.dashboard')
            ->with('success', "🔙 Switched back to admin: {$originalAdmin->name}");
    }

    /**
     * Get appropriate redirect route based on user role
     */
    private function getRedirectRoute(User $user): string
    {
        if ($user->hasRole('admin')) {
            return 'admin.dashboard';
        } elseif ($user->hasRole('depot-admin')) {
            return 'depot.dashboard';
        } elseif ($user->hasRole('site-admin')) {
            return 'site.dashboard';
        } elseif ($user->hasRole('customer')) {
            return 'customer.bookings.index';
        }
        
        return 'dashboard';
    }
}