<?php

namespace App\Http\Controllers;

use App\Models\suppliers;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class SuppliersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $suppliers = suppliers::orderBy('id','asc')->paginate(10);
        return view('dashboard.suppliers', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("dashboard.suppliers-add");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone_number' => 'required',
            'address' => 'required',
       ]);
       $suppliers = new suppliers();
       $suppliers->name = $request->name;
       $suppliers->phone_number = $request->phone_number;
       $suppliers->address = $request->address;
       $suppliers->save();
       return redirect()->route('admin.suppliers')->with('status','Record has been added successfully !');
    
    }

    /**
     * Display the specified resource.
     */
    public function show(suppliers $suppliers)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $supplier = suppliers::find($id);
        return view('dashboard.suppliers-edit',compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, suppliers $suppliers)
    {
        $request->validate([
            'name' => 'required',
            'phone_number' => 'required',
            'address' => 'required',
        ]);
        $suppliers = suppliers::find($request->id);
        $suppliers->name = $request->name;
        $suppliers->phone_number = $request->phone_number;
        $suppliers->address = $request->address;
        $suppliers->save();        
        return redirect()->route('admin.suppliers')->with('status','Record has been updated successfully !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $suppliers = suppliers::find($id);
        $suppliers->delete();
        return redirect()->route('admin.suppliers')->with('status','Record has been deleted successfully !');

    }
}
