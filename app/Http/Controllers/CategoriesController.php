<?php

namespace App\Http\Controllers;

use App\Models\categories;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Categories::orderBy('id','asc')->paginate(10);
        return view('dashboard.categories', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("dashboard.categories-add");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
       ]);
       $categories = new categories();
       $categories->name = $request->name;
       $categories->slug = Str::slug($request->name);
       $categories->save();
       return redirect()->route('admin.categories')->with('status','Record has been added successfully !');
    
    }

    /**
     * Display the specified resource.
     */
    public function show(categories $categories)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $categories = categories::find($id);
        return view('dashboard.categories-edit',compact('categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, categories $categories)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,'.$request->id,
        ]);
        $categories = categories::find($request->id);
        $categories->name = $request->name;
        $categories->slug = $request->slug;     
        $categories->save();        
        return redirect()->route('admin.categories')->with('status','Record has been updated successfully !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $categories = categories::find($id);
        $categories->delete();
        return redirect()->route('admin.categories')->with('status','Record has been deleted successfully !');

    }
}
