<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerDepotProduct;
use App\Models\Customer;
use App\Models\Depot;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerDepotProductController extends Controller
{
    public function index()
    {
        $items = CustomerDepotProduct::with(['customer', 'depot', 'product'])->get();
        return view('admin.customer_depot_products.index', compact('items'));
    }

    public function create()
    {
        $customers = Customer::all();
        $depots    = Depot::all();
        $products  = Product::all();
        return view('admin.customer_depot_products.create', compact('customers', 'depots', 'products'));
    }

    public function store(Request $request)
{
    $data = $request->validate([
        'customer_id' => ['required','exists:customers,id'],
        'depot_id'    => ['required','exists:depots,id'],
        'product_id'  => [
            'required','exists:products,id',
            Rule::unique('customer_depot_product')
                ->where(fn($q) => $q
                    ->where('customer_id', $request->customer_id)
                    ->where('depot_id',    $request->depot_id)
                )
        ],
        'min_cases'                 => 'nullable|integer|min:0',
        'max_cases'                 => 'nullable|integer|min:0',
        'override_duration_minutes' => 'nullable|integer|min:0',
    ], [
        'product_id.unique' => 'A rule for that customer + depot + product already exists.'
    ]);

    CustomerDepotProduct::create($data);

    return redirect()->route('admin.customer-depot-products.index')
                     ->with('success','Rule added successfully');
}

    public function edit(CustomerDepotProduct $item)
    {
        $customers = Customer::all();
        $depots    = Depot::all();
        $products  = Product::all();
        return view('admin.customer_depot_products.edit', compact('item', 'customers', 'depots', 'products'));
    }

    public function update(Request $request, CustomerDepotProduct $item)
{
    $data = $request->validate([
        'customer_id' => ['required','exists:customers,id'],
        'depot_id'    => ['required','exists:depots,id'],
        'product_id'  => [
            'required','exists:products,id',
            Rule::unique('customer_depot_product')
                ->ignore($item->id)
                ->where(fn($q) => $q
                    ->where('customer_id', $request->customer_id)
                    ->where('depot_id',    $request->depot_id)
                )
        ],
        // ... other fields ...
    ], [
        'product_id.unique' => 'A rule for that customer + depot + product already exists.'
    ]);

    $item->update($data);

    return redirect()->route('admin.customer-depot-products.index')
                     ->with('success','Rule updated successfully');
}

    public function destroy(CustomerDepotProduct $item)
    {
        $item->delete();
        return back()->with('success', 'Rule removed successfully');
    }
}