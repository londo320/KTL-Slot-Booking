<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Depot;
use App\Models\Product;
use Illuminate\Http\Request;

class DepotProductController extends Controller
{
    public function index(Depot $depot)
    {
        $products = Product::orderBy('sku')->get();

        return view('admin.depots.products.index', [
            'depot' => $depot,
            'products' => $products,
            'assigned' => $depot->products()->withPivot(['min_cases', 'max_cases', 'duration_override_minutes'])->get()->keyBy('id'),
        ]);
    }

    public function update(Request $request, Depot $depot)
    {
        $data = $request->input('products', []);

        $sync = [];

        foreach ($data as $productId => $values) {
            $sync[$productId] = [
                'min_cases' => $values['min_cases'] ?? 0,
                'max_cases' => $values['max_cases'] ?? 0,
                'duration_override_minutes' => $values['duration_override_minutes'] ?? null,
            ];
        }

        $depot->products()->sync($sync);

        return back()->with('success', 'Depot product rules updated.');
    }
}
