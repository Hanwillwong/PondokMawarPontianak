<?php

namespace App\Http\Controllers;

use App\Models\cart;
use App\Models\user_addresses;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\products;
use App\Models\categories;
use Midtrans\Snap;
use Midtrans\Config;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil cart dari session
        $cart = session()->get('cart', []);

        // Ambil semua product_id dari key array cart
        $productIds = array_keys($cart);

        // Ambil data produk dari database dengan relasi product_price
        $products = Products::with('product_price')
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id'); // Supaya mudah dicari berdasarkan ID

        // Gabungkan data dari session cart dan database
        $cartDetails = [];

        foreach ($cart as $productId => $item) {
            $product = $products[$productId] ?? null;

            if ($product) {
                $cartDetails[] = [
                    'product' => $product,                 // Data lengkap dari database
                    'quantity' => $item['quantity'],       // Jumlah dari session
                    'price' => $item['price'],             // Harga dari session (opsional)
                    'image' => $item['image'],             // Gambar dari session
                ];
            }
        }

        return view('pages.cart', compact('cartDetails'));
    }


    public function add(Request $request)
    {
        $product = Products::with('product_price')->findOrFail($request->id);
        $quantity = $request->input('quantity', 1);

        $cart = session()->get('cart', []);

        $currentQtyInCart = isset($cart[$product->id]) ? $cart[$product->id]['quantity'] : 0;
        $newTotalQty = $currentQtyInCart + $quantity;

        // ✅ Cek stok
        if ($newTotalQty > $product->quantity) {
            return redirect()->back()->with('error', 'Jumlah melebihi stok yang tersedia.');
        }

        // ✅ Cari harga grosir berdasarkan total quantity
        $applicablePrice = $product->price;

        foreach ($product->product_price as $priceTier) {
            if ($newTotalQty >= $priceTier->min_quantity) {
                $applicablePrice = $priceTier->price;
            }
        }

        // ✅ Update cart
        $cart[$product->id] = [
            "name" => $product->name,
            "quantity" => $newTotalQty,
            "price" => $applicablePrice,
            "image" => $product->image
        ];

        session()->put('cart', $cart);

        return redirect()->route('product.show', ['id' => $product->id])->with('success', 'Product added to cart!');
    }



    public function update(Request $request)
    {
        // Ambil data cart dari session
        $cart = session()->get('cart', []);

        // Ambil ID produk dan quantity yang dikirimkan
        $id = $request->id;
        $quantity = $request->quantity;

        // Pastikan produk ada di cart
        if (isset($cart[$id])) {
            // Update quantity di cart
            $cart[$id]['quantity'] = $quantity;
            
            // Simpan kembali ke session
            session()->put('cart', $cart);
        }

        // Kembalikan response JSON
        return response()->json(['success' => true, 'cart' => $cart]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }


    /**
     * Display the specified resource.
     */
    public function show(cart $cart)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(cart $cart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
 

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        // dd($request->all());

        $cart = session()->get('cart');
        $id = $request->id;

        if ($cart && isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Item removed from cart!');
    }

    public function index_checkout()
    {
        $cart = session()->get('cart', []);

        $productIds = array_keys($cart);

        $products = Products::with('product_price')
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $cartDetails = [];
        $total = 0;

        foreach ($cart as $productId => $item) {
            $product = $products[$productId] ?? null;

            if ($product) {
                // Cek kembali harga grosir berdasarkan quantity
                $quantity = $item['quantity'];
                $applicablePrice = $product->price;

                foreach ($product->product_price as $tier) {
                    if ($quantity >= $tier->min_quantity) {
                        $applicablePrice = $tier->price;
                    }
                }

                $subtotal = $applicablePrice * $quantity;
                $total += $subtotal;

                $cartDetails[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'price' => $applicablePrice,
                    'image' => $item['image'],
                    'subtotal' => $subtotal
                ];
            }
        }

        $addresses = user_addresses::where('user_id', auth()->id())->get();

        return view("pages.checkout", compact('cartDetails', 'total', 'addresses'));
    }

    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function createSnapToken(Request $request)
    {
        $user = auth()->user();
        $cart = session()->get('cart', []);
        $productIds = array_keys($cart);

        $products = Products::whereIn('id', $productIds)->get()->keyBy('id');

        $total = 0;
        $items = [];

        foreach ($cart as $productId => $item) {
            $product = $products[$productId];
            $price = $item['price'];
            $qty = $item['quantity'];
            $subtotal = $price * $qty;
            $total += $subtotal;

            $items[] = [
                'id' => $product->id,
                'price' => $price,
                'quantity' => $qty,
                'name' => $product->name,
            ];
        }

        $orderId = 'ORDER-' . Str::uuid();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $total,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone_number,
            ],
            'item_details' => $items,
        ];

        $snapToken = Snap::getSnapToken($params);

        return response()->json([
            'snapToken' => $snapToken,
            'order_id' => $orderId
        ]);
    }
}
