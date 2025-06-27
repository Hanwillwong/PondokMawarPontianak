<?php

namespace App\Http\Controllers;

use App\Models\brands;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = brands::orderBy('id','asc')->paginate(10);
        return view("dashboard.brands",compact('brands'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("dashboard.brand-add");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
       ]);
       $brand = new brands();
       $brand->name = $request->name;
       $brand->save();
       return redirect()->route('admin.brands')->with('status','Record has been added successfully !');
    }

    /**
     * Display the specified resource.
     */
    public function show(brands $brands)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $brand = brands::find($id);
        return view('dashboard.brand-edit',compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, brands $brands)
    {
        $request->validate([
            'name' => 'required',
        ]);
        $brand = brands::find($request->id);
        $brand->name = $request->name;
        $brand->save();        
        return redirect()->route('admin.brands')->with('status','Record has been updated successfully !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $brand = brands::find($id);
        $brand->delete();
        return redirect()->route('admin.brands')->with('status','Record has been deleted successfully !');

    }
}
