<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProductCategoryController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:PRODUCT_CATEGORY_LIST|PRODUCT_CATEGORY_ADD|PRODUCT_CATEGORY_EDIT|PRODUCT_CATEGORY_DELETE', ['only' => ['index', 'show']]);
        $this->middleware('permission:PRODUCT_CATEGORY_ADD', ['only' => ['create', 'store']]);
        $this->middleware('permission:PRODUCT_CATEGORY_EDIT', ['only' => ['edit', 'update']]);
        $this->middleware('permission:PRODUCT_CATEGORY_DELETE', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $perPage = 20;
        $data = ProductCategory::orderBy('name', 'asc')->paginate($perPage);
        
        return view('admin.pages.product_categories.index', compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * $perPage);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.pages.product_categories.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:100', 'unique:product_categories,name'],
            'description' => ['nullable', 'string'],
        ]);

        ProductCategory::create($request->all());

        return redirect()->route('mindo.product-categories.index')
            ->with('message', 'Product Category created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductCategory $productCategory): View
    {
        return view('admin.pages.product_categories.show', compact('productCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductCategory $productCategory): View
    {
        return view('admin.pages.product_categories.form', compact('productCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductCategory $productCategory): RedirectResponse
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:100', 'unique:product_categories,name,' . $productCategory->id],
            'description' => ['nullable', 'string'],
        ]);

        $productCategory->update($request->all());

        return redirect()->route('mindo.product-categories.index')
            ->with('message', 'Product Category updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductCategory $productCategory): RedirectResponse
    {
        // Check if the category is in use by any products
        if ($productCategory->products()->count() > 0) {
            return redirect()->route('mindo.product-categories.index')
                ->with([
                    'message' => 'Cannot delete category: It is being used by products!',
                    'alert-type' => 'error'
                ]);
        }
        
        $productCategory->delete();

        return redirect()->route('mindo.product-categories.index')
            ->with('message', 'Product Category deleted successfully!');
    }
}
