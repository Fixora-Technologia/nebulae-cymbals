<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class UnitController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:UNIT_LIST|UNIT_ADD|UNIT_EDIT|UNIT_DELETE', ['only' => ['index', 'show']]);
        $this->middleware('permission:UNIT_ADD', ['only' => ['create', 'store']]);
        $this->middleware('permission:UNIT_EDIT', ['only' => ['edit', 'update']]);
        $this->middleware('permission:UNIT_DELETE', ['only' => ['destroy']]);
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $perPage = 20;
        $data = Unit::orderBy('name', 'asc')->paginate($perPage);
        
        return view('admin.pages.units.index', compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * $perPage);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.pages.units.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:20', 'unique:units,name'],
            'abbreviation' => ['required', 'string', 'max:10'],
        ]);

        Unit::create($request->all());

        return redirect()->route('mindo.units.index')
            ->with('message', 'Unit created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Unit $unit): View
    {
        return view('admin.pages.units.show', compact('unit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unit $unit): View
    {
        return view('admin.pages.units.form', compact('unit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit): RedirectResponse
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:20', 'unique:units,name,' . $unit->id],
            'abbreviation' => ['required', 'string', 'max:10'],
        ]);

        $unit->update($request->all());

        return redirect()->route('mindo.units.index')
            ->with('message', 'Unit updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit): RedirectResponse
    {
        // Check if the unit is in use by any products
        if ($unit->products()->count() > 0) {
            return redirect()->route('mindo.units.index')
                ->with([
                    'message' => 'Cannot delete unit: It is being used by products!',
                    'alert-type' => 'error'
                ]);
        }
        
        $unit->delete();

        return redirect()->route('mindo.units.index')
            ->with('message', 'Unit deleted successfully!');
    }
}
