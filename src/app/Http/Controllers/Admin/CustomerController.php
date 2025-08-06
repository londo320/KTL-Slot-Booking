<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    // Display a listing of customers
    public function index()
    {
        $customers = Customer::with('users')->paginate(25);
        return view('admin.customers.index', compact('customers'));
    }

    // Show the form for creating a new customer
    public function create()
    {
        $users = User::orderBy('name')->get();
        return view('admin.customers.create', compact('users'));
    }

    // Store a newly created customer in storage
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
          // ' 'email'     => 'required|email|unique:customers,email',
            'user_ids'  => 'nullable|array',
            'user_ids.*'=> 'exists:users,id',
        ]);

        // Create customer
        $customer = Customer::create([
            'name'  => $data['name'],
          //  'email' => $data['email'],
        ]);

        // Assign selected users using many-to-many relationship
        if (!empty($data['user_ids'])) {
            $customer->users()->sync($data['user_ids']);
        }

        return redirect()->route('admin.customers.index')
                         ->with('success', 'Customer created successfully.');
    }

    // Show the form for editing the specified customer
    public function edit(Customer $customer)
    {
        $users = User::orderBy('name')->get();
        $customer->load('users');
        return view('admin.customers.edit', compact('customer', 'users'));
    }

    // Update the specified customer in storage
    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
      //      'email'     => "required|email|unique:customers,email,{$customer->id}",
            'user_ids'  => 'nullable|array',
            'user_ids.*'=> 'exists:users,id',
        ]);

        // Prevent assigning a user to multiple customers (excluding current)
        // Update customer
        $customer->update([
            'name'  => $data['name'],
            'email' => $data['email'],
        ]);

        // Sync users using many-to-many relationship
        $customer->users()->sync($data['user_ids'] ?? []);

        return redirect()->route('admin.customers.index')
                         ->with('success', 'Customer updated successfully.');
    }

    // Remove the specified customer from storage
    public function destroy(Customer $customer)
    {
        // Detach all users using many-to-many relationship
        $customer->users()->detach();

        $customer->delete();

        return redirect()->route('admin.customers.index')
                         ->with('success', 'Customer deleted successfully.');
    }
}
