<?php

namespace App\Http\Controllers;

use App\Models\cart;
use App\Models\user_addresses;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\products;
use App\Models\status;
use App\Models\orders;
use App\Models\order_details;
use App\Models\categories;
use Midtrans\Snap;
use Midtrans\Config;
use Exception;


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

    public function index_confirmation() {
        return view('pages.order-confirmation');
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
    public function store(Request $request)
    {
        $user = auth()->user();
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Keranjang belanja kosong.');
        }

        $purchase_type = $request->input('shipping_method'); // pickup / delivery
        $paymentMethod = $request->input('payment_method');   // cod / midtrans
        $addressId = $request->input('address_id');           // required jika delivery

        if ($purchase_type === 'delivery' && $paymentMethod === 'cod') {
            return response()->json(['error' => 'COD hanya tersedia untuk pickup'], 400);
        }
        
        if ($purchase_type === 'delivery' && !$addressId) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Silakan pilih alamat pengiriman.'], 400);
            }
            return back()->with('error', 'Silakan pilih alamat pengiriman.');
        }

        // Ambil produk dari database
        $productIds = array_keys($cart);
        $products = \App\Models\products::with('product_price')->whereIn('id', $productIds)->get()->keyBy('id');

        $total = 0;
        $orderDetails = [];

        foreach ($cart as $productId => $item) {
            $product = $products[$productId] ?? null;
            if (!$product) continue;

            $quantity = $item['quantity'];
            $price = $product->price;

            foreach ($product->product_price as $tier) {
                if ($quantity >= $tier->min_quantity) {
                    $price = $tier->price;
                }
            }

            $subtotal = $price * $quantity;
            $total += $subtotal;

            $orderDetails[] = [
                'product_id' => $product->id,
                'price_at_order' => $price,
                'quantity' => $quantity,
                'subtotal' => $subtotal,
            ];
        }

        // Simpan order
        $order = new \App\Models\orders();
        $order->user_id = $user->id;
        $order->status_id = Status::where('label', $paymentMethod === 'midtrans' ? 'pending' : 'processing')->first()->id;
        $order->total_price = $total;
        $order->payment_method = $paymentMethod;
        $order->purchase_type = $purchase_type;
        $order->address_id = $purchase_type === 'delivery' ? $addressId : null;
        $order->reference_number = 'ORD-' . strtoupper(Str::random(10));
        $order->save();

        // Setelah simpan order dan order_details
        foreach ($orderDetails as $detail) {
            $detail['order_id'] = $order->id;
            order_details::create($detail);
        }

        // Buat item_details untuk Midtrans
        $items = [];
        foreach ($order->order_detail as $detail) {
            $product = Products::find($detail['product_id']);
            $items[] = [
                'id' => $detail['product_id'],
                'price' => $detail['price_at_order'],
                'quantity' => $detail['quantity'],
                'name' => Str::limit(preg_replace('/[^A-Za-z0-9 ]/', '', $product->name), 50),
            ];
        }

        $address = $order->address_id ? user_addresses::find($order->address_id) : null;

        $params = [
            'transaction_details' => [
                'order_id' => $order->reference_number,
                'gross_amount' => max($order->total_price, 100),
            ],
            'item_details' => $items,
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
                'shipping_address' => $address ? [
                    'first_name' => $address->name,
                    'phone' => $address->phone,
                    'address' => $address->address,
                    'city' => $address->city,
                    'postal_code' => $address->post_code,
                    'country_code' => 'IDN',
                ] : [],
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        // ✅ Kosongkan cart (opsional)
        session()->forget('cart');

        // ✅ Return Snap Token agar langsung bisa dipakai untuk memunculkan popup
        return response()->json([
            'success' => true,
            'snapToken' => $snapToken,
            'order_id' => $order->id,
        ]);

    }



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
        $this->middleware('auth');

        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function createSnapToken(Request $request)
    {
        try {
            $order = Orders::with('user', 'order_detail.product')
                ->where('user_id', auth()->id())
                ->where('id', $request->order_id)
                ->first();
            if (!$order) {
                return response()->json(['error' => 'Order tidak ditemukan'], 404);
            }

            $items = [];
            foreach ($order->order_detail as $detail) {
                $items[] = [
                    'id' => $detail->product_id,
                    'price' => $detail->price_at_order,
                    'quantity' => $detail->quantity,
                    'name' => Str::limit(preg_replace('/[^A-Za-z0-9 ]/', '', $detail->product->name), 50),
                ];
            }

            $address = $order->address_id ? \App\Models\user_addresses::find($order->address_id) : null;

            $params = [
                'transaction_details' => [
                    'order_id' => $order->reference_number,
                    'gross_amount' => max($order->total_price, 100),
                ],
                'item_details' => $items,
                'customer_details' => [
                    'first_name' => $order->user->name,
                    'email' => $order->user->email,
                    'shipping_address' => $address ? [
                        'first_name'   => $address->name,
                        'phone'        => $address->phone,
                        'address'      => $address->address,
                        'city'         => $address->city,
                        'postal_code'  => $address->post_code,
                        'country_code' => 'IDN',
                    ] : [],
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

            return response()->json([
                'snapToken' => $snapToken,
                'order_id' => $order->id,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }

}
