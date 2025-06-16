<?php

namespace App\Http\Controllers;

use App\Models\products;
use App\Models\categories;
use App\Models\brands;
use App\Models\product_prices;
use App\Models\suppliers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Products::with(['brand', 'supplier', 'category'])->orderBy('id', 'asc')->paginate(10);
        return view("dashboard.products",compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = categories::Select('id','name')->orderBy('id')->get();
        $brands = Brands::Select('id','name')->orderBy('id')->get();
        $suppliers = suppliers::Select('id','name')->orderBy('id')->get();

        return view("dashboard.products-add",compact('categories','brands','suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Debug untuk memastikan data yang dikirimkan
        // dd($request->all());

        //  Validasi input dari form
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug',
            'category_id' => 'required',
            'brand_id' => 'required',
            'supplier_id' => 'required',
            'description' => 'required',
            'quantity' => 'required|numeric|min:0',
            'image' => 'required|mimes:png,jpg,jpeg|max:2048', // Validasi gambar
            'prices' => 'required|array',
            'prices.*.min_qty' => 'required|numeric|min:1',
            'prices.*.price' => 'required|numeric|min:0',
        ]);

        // Membuat objek produk baru
        $product = new products();
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->description = $request->description;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
        $product->supplier_id = $request->supplier_id;

        // Memeriksa apakah ada gambar yang diupload
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // Mengambil file gambar dan menyimpannya
            $image = $request->file('image');
            $imageName = time() . '.' . $image->extension();
            $image->move(public_path('uploads/products'), $imageName); // Menyimpan gambar ke folder uploads/products
            $product->image = $imageName; // Menyimpan nama gambar ke database
        }

        // Menyimpan produk ke dalam database
        $product->save();

        // Menyimpan harga ke dalam product_prices dan harga pertama ke dalam produk
        foreach ($request->prices as $product_prices) {
            // Simpan semua harga ke tabel product_prices
            product_prices::create([
                'product_id' => $product->id,
                'min_quantity' => $product_prices['min_qty'],
                'price' => $product_prices['price'],
            ]);

            // Jika min_qty == 1, simpan harga pertama ke dalam produk
            if ($product_prices['min_qty'] == 1) {
                $product->price = $product_prices['price']; // Simpan harga ke produk
                $product->save(); // Update produk dengan harga tersebut
            }
        }

        // Mengarahkan kembali ke halaman produk dengan pesan status
        return redirect()->route('admin.products')->with('status', 'Record has been added successfully!');
    }



    /**
     * Display the specified resource.
     */
   public function show($id)
    {
        $product = Products::with('product_price')->findOrFail($id);

        $cart = session()->get('cart', []);
        $qtyInCart = isset($cart[$product->id]) ? $cart[$product->id]['quantity'] : 0;

        return view('pages.product-details', compact('product', 'qtyInCart'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $categories = categories::all();
        $brands = Brands::all();
        $suppliers = suppliers::all();
        $product = products::with('product_price')->findOrFail($id);

        return view('dashboard.products-edit', [
            'product' => $product,
            'categories' => $categories,
            'brands' => $brands,
            'suppliers' => $suppliers,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, products $product)
    {
        // Validate input
        // dd($request->all(), $product->id);
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug,' . $product->id, // Ensure we exclude current product from slug uniqueness check
            'category_id' => 'required',
            'brand_id' => 'required',
            'supplier_id' => 'required',
            'description' => 'required',
            'quantity' => 'required|numeric|min:0',
            'image' => 'nullable|mimes:png,jpg,jpeg|max:2048',
            'prices' => 'required|array',
            'prices.*.min_qty' => 'required|numeric|min:1',
            'prices.*.price' => 'required|numeric|min:0',
        ]);

        // Update product details
        $product->name = $request->name;
        $product->slug = $request->slug;
        $product->description = $request->description;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
        $product->supplier_id = $request->supplier_id;

        // Handle image upload
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->extension();
            $image->move(public_path('uploads/products'), $imageName);

            // Remove the old image if exists
            if ($product->image && file_exists(public_path('uploads/products/' . $product->image))) {
                unlink(public_path('uploads/products/' . $product->image));
            }

            $product->image = $imageName;
        }

        // Save the product
        $product->save();

        // Remove old prices (if full update required)
        product_prices::where('product_id', $product->id)->delete();

        // Save new prices
        foreach ($request->prices as $price) {
            product_prices::create([
                'product_id' => $product->id,
                'min_quantity' => $price['min_qty'],
                'price' => $price['price'],
            ]);

            // Set price to product if min_qty == 1
            if ($price['min_qty'] == 1) {
                $product->price = $price['price'];
                $product->save();
            }
        }

        return redirect()->route('admin.products')->with('status', 'Product has been updated successfully!');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $products = products::find($id);
        product_prices::where('product_id', $products->id)->delete();
        $products->delete();
        return redirect()->route('admin.products')->with('status','Record has been deleted successfully !');
    }
}
