<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CustomerController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:CUSTOMER_LIST|CUSTOMER_ADD|CUSTOMER_EDIT|CUSTOMER_DELETE', ['only' => ['index', 'show']]);
        $this->middleware('permission:CUSTOMER_ADD', ['only' => ['create', 'store']]);
        $this->middleware('permission:CUSTOMER_EDIT', ['only' => ['edit', 'update']]);
        $this->middleware('permission:CUSTOMER_DELETE', ['only' => ['destroy']]);
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $perPage = 20;
        $query = Customer::query();
        
        // Filter by search term if provided
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('contact', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        $data = $query->orderBy('name', 'asc')->paginate($perPage);
        
        return view('admin.pages.customers.index', compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * $perPage);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.pages.customers.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:100'],
            'contact' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
        ]);

        Customer::create($request->all());

        return redirect()->route('mindo.customers.index')
            ->with('message', 'Customer created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer): View
    {
        return view('admin.pages.customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer): View
    {
        return view('admin.pages.customers.form', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:100'],
            'contact' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
        ]);

        $customer->update($request->all());

        return redirect()->route('mindo.customers.index')
            ->with('message', 'Customer updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer): RedirectResponse
    {
        // Check if the customer is in use by any transactions
        if ($customer->transactions()->count() > 0) {
            return redirect()->route('mindo.customers.index')
                ->with([
                    'message' => 'Cannot delete customer: They have transaction records!',
                    'alert-type' => 'error'
                ]);
        }
        
        $customer->delete();

        return redirect()->route('mindo.customers.index')
            ->with('message', 'Customer deleted successfully!');
    }
}
