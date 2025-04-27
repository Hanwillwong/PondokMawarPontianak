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
            'slug' => 'required|unique:brands,slug',
       ]);
       $brand = new Brands();
       $brand->name = $request->name;
       $brand->slug = Str::slug($request->name);
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
        $brand = Brands::find($id);
        return view('dashboard.brand-edit',compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, brands $brands)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,'.$request->id,
        ]);
        $brand = Brands::find($request->id);
        $brand->name = $request->name;
        $brand->slug = $request->slug;     
        $brand->save();        
        return redirect()->route('admin.brands')->with('status','Record has been updated successfully !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $brand = Brands::find($id);
        $brand->delete();
        return redirect()->route('admin.brands')->with('status','Record has been deleted successfully !');

    }
}
