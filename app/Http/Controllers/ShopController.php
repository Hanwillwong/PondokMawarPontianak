<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\products;
use App\Models\brands;
use App\Models\categories;

class ShopController extends Controller
{
    public function index()
    {
        $brands = Brands::all();
        $categories = Categories::with('sampleProduct')->get();
        $products = Products::with(['brand', 'supplier', 'category'])->orderBy('id', 'asc')->paginate(10);
        return view('pages.shop',compact('products','categories','brands'));
    }
}
