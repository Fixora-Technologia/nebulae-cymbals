<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:PRODUCT_LIST|PRODUCT_ADD|PRODUCT_EDIT|PRODUCT_DELETE', ['only' => ['index', 'show']]);
        $this->middleware('permission:PRODUCT_ADD', ['only' => ['create', 'store']]);
        $this->middleware('permission:PRODUCT_EDIT', ['only' => ['edit', 'update']]);
        $this->middleware('permission:PRODUCT_DELETE', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $perPage = 20;
        $query = Product::with(['category', 'unit'])->orderBy('name', 'asc');

        // Filter by category if provided
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by low stock if requested
        if ($request->has('low_stock') && $request->low_stock) {
            $query->whereRaw('stock <= min_stock');
        }

        $data = $query->paginate($perPage);
        $categories = ProductCategory::orderBy('name', 'asc')->pluck('name', 'id');

        return view('admin.pages.products.index', compact('data', 'categories'))
            ->with('i', ($request->input('page', 1) - 1) * $perPage);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $categories = ProductCategory::orderBy('name', 'asc')->pluck('name', 'id');

        // Get units with abbreviations included in the display text
        $units = Unit::orderBy('name', 'asc')->get();
        $formattedUnits = [];
        foreach ($units as $unit) {
            $formattedUnits[$unit->id] = $unit->name . ' (' . $unit->abbreviation . ')';
        }
        $units = $formattedUnits;

        return view('admin.pages.products.form', compact('categories', 'units'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'category_id' => ['required', 'exists:product_categories,id'],
            'unit_id' => ['required', 'exists:units,id'],
            'name' => ['required', 'string', 'max:100'],
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $data = $request->all();

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->storeAs('public/products', $imageName);
            $data['image_path'] = 'products/' . $imageName;
        }

        Product::create($data);

        return redirect()->route('mindo.products.index')
            ->with('message', 'Product created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): View
    {
        return view('admin.pages.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product): View
    {
        $categories = ProductCategory::orderBy('name', 'asc')->pluck('name', 'id');

        // Get units with abbreviations included in the display text
        $units = Unit::orderBy('name', 'asc')->get();
        $formattedUnits = [];
        foreach ($units as $unit) {
            $formattedUnits[$unit->id] = $unit->name . ' (' . $unit->abbreviation . ')';
        }
        $units = $formattedUnits;

        return view('admin.pages.products.form', compact('product', 'categories', 'units'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $this->validate($request, [
            'category_id' => ['required', 'exists:product_categories,id'],
            'unit_id' => ['required', 'exists:units,id'],
            'name' => ['required', 'string', 'max:100'],
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku,' . $product->id],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $data = $request->all();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image_path && Storage::exists('public/' . $product->image_path)) {
                Storage::delete('public/' . $product->image_path);
            }

            // Upload new image
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->storeAs('public/products', $imageName);
            $data['image_path'] = 'products/' . $imageName;
        }

        $product->update($data);

        return redirect()->route('mindo.products.index')
            ->with('message', 'Product updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        // Check if the product is in use by any transaction items
        if ($product->transactionItems()->count() > 0) {
            return redirect()->route('mindo.products.index')
                ->with([
                    'message' => 'Cannot delete product: It has transaction records!',
                    'alert-type' => 'error'
                ]);
        }

        // Delete product image if exists
        if ($product->image_path && Storage::exists('public/' . $product->image_path)) {
            Storage::delete('public/' . $product->image_path);
        }

        $product->delete();

        return redirect()->route('mindo.products.index')
            ->with('message', 'Product deleted successfully!');
    }
}
