<?php

namespace App\Http\Controllers;

use App\Models\stockIn;
use App\Models\suppliers;
use App\Models\products;
use Illuminate\Http\Request;

class StockInController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stockIns = stockIn::with('product', 'supplier')->orderBy('created_at', 'desc')->paginate(10);
        return view('dashboard.stockin', compact('stockIns'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = products::orderBy('name')->get();
        $suppliers = suppliers::orderBy('name')->get();
        return view('dashboard.stockin-add', compact('products', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'date' => 'nullable|date',
        ]);

        // Simpan ke tabel stock_ins
        $stock = new stockIn();
        $stock->supplier_id = $request->supplier_id;
        $stock->product_id = $request->product_id;
        $stock->quantity = $request->quantity;
        $stock->date = $request->date ?? now();
        $stock->save();

        // Tambah ke stok produk
        $product = products::find($request->product_id);
        $product->quantity += $request->quantity;
        $product->save();

        return redirect()->route('admin.stockin')->with('status', 'Stok berhasil ditambahkan.');
    
    }

    /**
     * Display the specified resource.
     */
    public function show(stockIn $stockIn)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit( $id)
    {
        $stockIn = stockIn::findOrFail($id);
        $products = products::orderBy('name')->get();
        $suppliers = suppliers::orderBy('name')->get();

        return view('dashboard.stockin-edit', compact('stockIn', 'products', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $id)
    {
        $stock = stockIn::findOrFail($id);

        $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'date' => 'nullable|date',
        ]);

        // Kurangi stok lama
        $oldProduct = products::find($stock->product_id);
        $oldProduct->stock -= $stock->quantity;
        $oldProduct->save();

        // Update data
        $stock->supplier_id = $request->supplier_id;
        $stock->product_id = $request->product_id;
        $stock->quantity = $request->quantity;
        $stock->date = $request->date ?? now();
        $stock->save();

        // Tambah stok baru
        $newProduct = products::find($request->product_id);
        $newProduct->stock += $request->quantity;
        $newProduct->save();

        return redirect()->route('dashboard.stockin')->with('status', 'Data stok berhasil diperbarui.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $stock = stockIn::findOrFail($id);

        // Kurangi stok produk
        $product = products::find($stock->product_id);
        if ($product && $product->stock >= $stock->quantity) {
            $product->stock -= $stock->quantity;
            $product->save();
        }

        $stock->delete();

        return redirect()->route('dashboard.stockin')->with('status', 'Data stok berhasil dihapus.');
    
    }
}
