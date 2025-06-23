<?php

namespace App\Http\Controllers;
use App\Models\products;
use App\Models\categories;
use App\Models\brands;
use App\Models\product_prices;
use App\Models\suppliers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function index()
    {
        $categories = categories::with('sampleProduct')->get();
        $products = products::with(['brand', 'supplier', 'category'])->orderBy('id', 'asc')->paginate(10);
        return view('pages.index',compact('products','categories'));
    }

}
