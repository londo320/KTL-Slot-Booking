<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:admin']);
    }

    public function index()
    {
        $products = Product::orderBy('sku')->paginate(25);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create', ['product' => new Product]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sku'                => 'required|string|max:255|unique:products,sku',
            'description'        => 'nullable|string',
            'default_case_count' => 'nullable|integer|min:0',
            'default_pallets'    => 'nullable|integer|min:0',
        ]);

        Product::create($data);

        return redirect()->route('admin.products.index')
                         ->with('success','Product created successfully');
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'sku'                => "required|string|max:255|unique:products,sku,{\$product->id}",
            'description'        => 'nullable|string',
            'default_case_count' => 'nullable|integer|min:0',
            'default_pallets'    => 'nullable|integer|min:0',
        ]);

        $product->update($data);

        return redirect()->route('admin.products.index')
                         ->with('success','Product updated successfully');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')
                         ->with('success','Product deleted successfully');
    }
}
