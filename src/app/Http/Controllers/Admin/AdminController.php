<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; // Import the base controller
use App\Models\Depot;
use App\Models\Customer;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    // Display the list of users
    public function index()
    {
        $users = User::with(['roles', 'depots', 'customers'])->paginate(15);
        $roles = Role::all(); // Get all roles for the dropdown
        $depots = Depot::all();
        $customers = Customer::all();  // Get all customers for selection

        return view('admin.users.index', compact('users', 'roles', 'depots', 'customers'));
    }

    // Show the form for editing a user
    public function edit($id)
    {
        $user = User::with(['roles', 'depots', 'customers'])->findOrFail($id);
        $roles = Role::all(); // Get all roles for the checkboxes
        $depots = Depot::all();
        $customers = Customer::all();  // Get all customers for selection

        return view('admin.users.edit', compact('user', 'roles', 'depots', 'customers'));
    }

    // Update the user's data
    public function update(Request $request, $id)
    {
        // Validate the input data
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|max:255|unique:users,email,' . $id,
            'role_ids'       => 'required|array',           // Multiple roles
            'role_ids.*'     => 'integer|exists:roles,id',
            'customer_ids'   => 'nullable|array',            // Multiple customers (optional)
            'customer_ids.*' => 'exists:customers,id',       // Ensure all selected customers are valid
            'depot_ids'      => 'required|array',            // Ensure at least one depot is selected
            'depot_ids.*'    => 'exists:depots,id',           // Ensure all selected depots are valid
        ]);

        // Find the user by ID
        $user = User::findOrFail($id);

        // Update user basic fields
        $user->name        = $validated['name'];
        $user->email       = $validated['email'];
        $user->save();

        // Sync multiple roles via pivot
        $user->roles()->sync($validated['role_ids']);

        // Sync depots (many-to-many relationship)
        $user->depots()->sync($validated['depot_ids']);

        // Sync multiple customers (many-to-many relationship)
        $customerIds = $validated['customer_ids'] ?? [];
        $user->customers()->sync($customerIds);

        // If the password reset checkbox was checked, generate a new password
        if ($request->filled('reset_password')) {
            $newPassword = $this->generatePassword();
            $user->password = bcrypt($newPassword);
            $user->save();

            return redirect()->route('admin.users.edit', $user->id)
                         ->with('success', 'User updated successfully.')
                         ->with('new_password', $newPassword);
        }

        return redirect()->route('admin.users.index')
                         ->with('success', 'User updated successfully.');
    }

    // Helper method to generate a random password
    private function generatePassword($length = 8)
    {
        // Generate a password of 3 words with at least 8 characters
        $words = ['apple', 'banana', 'cherry', 'date', 'elderberry', 'fig', 'grape', 'honeydew'];
        $password = implode('', array_map(function() use ($words) {
            return $words[array_rand($words)];
        }, range(1, 3))); // Generate 3 random words

        // Ensure the password is at least 8 characters
        return strlen($password) < $length
            ? $password . rand(10, 99)
            : $password;
    }

    // Store method for creating a user
    public function store(Request $request)
    {

            // DEBUG: dump all input
   // dd($request->all());

        // Validate the input data
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => 'required|email|max:255|unique:users,email',
            'role_ids'          => 'required|array',           // Multiple roles
            'role_ids.*'        => 'integer|exists:roles,id',
            'customer_ids'      => 'nullable|array',            // Multiple customers (optional)
            'customer_ids.*'    => 'exists:customers,id',       // Ensure all selected customers are valid
            'depot_ids'         => 'required|array',
            'depot_ids.*'       => 'exists:depots,id',
            'password'          => 'required_without:generate_password|string|min:8',
            'generate_password' => 'nullable|boolean',
        ]);

        // Handle password generation
        $password = $validated['password'] ?? null;
        if ($request->filled('generate_password') || !$password) {
            $password = $this->generatePassword();
        }

        $user = User::create([
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'password'    => bcrypt($password),
        ]);

        // Sync multiple roles via pivot
        $user->roles()->sync($validated['role_ids']);

        // Sync depots
        $user->depots()->sync($validated['depot_ids']);

        // Sync multiple customers (many-to-many relationship)
        $customerIds = $validated['customer_ids'] ?? [];
        $user->customers()->sync($customerIds);

        // Show generated password if applicable
        if ($request->filled('generate_password') || !$validated['password']) {
            return redirect()->route('admin.users.edit', $user->id)
                         ->with('success', 'User created successfully.')
                         ->with('new_password', $password);
        }

        return redirect()->route('admin.users.index')
                         ->with('success', 'User created successfully.');
    }

    public function create()
    {
        $roles = Role::all();
        $depots = Depot::all();
        $customers = Customer::all();

        return view('admin.users.create', compact('roles', 'depots', 'customers'));
    }
}
