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

        // Prevent assigning a user to multiple customers
        if (!empty($data['user_ids'])) {
            $alreadyAssigned = User::whereIn('id', $data['user_ids'])
                ->whereNotNull('customer_id')
                ->pluck('name')
                ->toArray();

            if (count($alreadyAssigned)) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'user_ids' => 'These users are already assigned: ' . implode(', ', $alreadyAssigned)
                    ]);
            }
        }

        // Create customer
        $customer = Customer::create([
            'name'  => $data['name'],
          //  'email' => $data['email'],
        ]);

        // Clear any previous assignments (none on create)
        User::where('customer_id', $customer->id)
            ->update(['customer_id' => null]);

        // Assign selected users by setting their customer_id
        if (!empty($data['user_ids'])) {
            User::whereIn('id', $data['user_ids'])
                ->update(['customer_id' => $customer->id]);
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
        if (!empty($data['user_ids'])) {
            $alreadyAssigned = User::whereIn('id', $data['user_ids'])
                ->where('customer_id', '!=', $customer->id)
                ->pluck('name')
                ->toArray();

            if (count($alreadyAssigned)) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'user_ids' => 'These users are already assigned: ' . implode(', ', $alreadyAssigned)
                    ]);
            }
        }

        // Update customer
        $customer->update([
            'name'  => $data['name'],
            'email' => $data['email'],
        ]);

        // Clear old assignments
        User::where('customer_id', $customer->id)
            ->update(['customer_id' => null]);

        // Assign new users
        if (!empty($data['user_ids'])) {
            User::whereIn('id', $data['user_ids'])
                ->update(['customer_id' => $customer->id]);
        }

        return redirect()->route('admin.customers.index')
                         ->with('success', 'Customer updated successfully.');
    }

    // Remove the specified customer from storage
    public function destroy(Customer $customer)
    {
        // Detach users
        User::where('customer_id', $customer->id)
            ->update(['customer_id' => null]);

        $customer->delete();

        return redirect()->route('admin.customers.index')
                         ->with('success', 'Customer deleted successfully.');
    }
}
