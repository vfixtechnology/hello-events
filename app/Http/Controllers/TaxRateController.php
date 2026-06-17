<?php

namespace App\Http\Controllers;

use App\Models\TaxRate;
use Illuminate\Http\Request;

class TaxRateController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:tax list')->only(['index']);
        $this->middleware('can:tax create')->only(['create', 'store']);
        $this->middleware('can:tax edit')->only(['edit', 'update']);
        $this->middleware('can:tax delete')->only(['destroy']);
    }

    public function index()
    {
        $taxes = TaxRate::all();
        return view('backend.tax.index',compact('taxes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255|unique:tax_rates,title',
            'rate' => 'required|numeric|min:0|max:100|decimal:0,2',
            'is_active' => 'boolean',
        ]);

        TaxRate::create($data);
        return redirect()->back()->withSuccess('Tax Rate has been added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(TaxRate $tax)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TaxRate $tax)
    {
        return view('backend.tax.edit',compact('tax'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TaxRate $tax)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255|unique:tax_rates,title,'.$tax->id,
            'rate' => 'required|numeric|min:0|max:100|decimal:0,2',
            'is_active' => 'boolean',
        ]);

        $tax->update($data);
        return redirect()->route('tax.index')->withSuccess('Tax rate has been update successfully!');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TaxRate $tax)
    {
        if ($tax->events()->count()) {
            return back()->with('error', 'Tax rate cannot be deleted — it is linked to events.');
        }
        $tax->delete();
        return back()->withSuccess('Tax rate has been deleted successfully!');
    }
}
