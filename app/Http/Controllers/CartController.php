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
use App\Models\PushSubscription;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use Illuminate\Support\Facades\Log;
use Midtrans\Notification;
use App\Models\temp_orders;



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
        $products = products::with('product_price')
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

    public function index_confirmation(Request $request)
    {
        $orderRef = $request->query('order_ref');

        $order = orders::with(['order_detail.product'])->where('reference_number', $orderRef)->first();

        if (!$order) {
            return redirect()->route('home')->with('error', 'Order tidak ditemukan.');
        }

        return view('pages.order-confirmation', compact('order'));
    }


    public function add(Request $request)
    {
        $product = products::with('product_price')->findOrFail($request->id);
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
    // public function store(Request $request)
    // {
    //     $user = auth()->user();
    //     $cart = session('cart', []);

    //     if (empty($cart)) {
    //         return redirect()->route('cart')->with('error', 'Keranjang belanja kosong.');
    //     }

    //     $purchase_type = $request->input('shipping_method'); // pickup / delivery
    //     $paymentMethod = $request->input('payment_method');   // cod / midtrans
    //     $addressId = $request->input('address_id');           // required jika delivery

    //     if ($purchase_type === 'delivery' && $paymentMethod === 'cod') {
    //         return response()->json(['error' => 'COD hanya tersedia untuk pickup'], 400);
    //     }
        
    //     if ($purchase_type === 'delivery' && !$addressId) {
    //         if ($request->expectsJson()) {
    //             return response()->json(['error' => 'Silakan pilih alamat pengiriman.'], 400);
    //         }
    //         return back()->with('error', 'Silakan pilih alamat pengiriman.');
    //     }

    //     // Ambil produk dari database
    //     $productIds = array_keys($cart);
    //     $products = \App\Models\products::with('product_price')->whereIn('id', $productIds)->get()->keyBy('id');

    //     $total = 0;
    //     $orderDetails = [];

    //     foreach ($cart as $productId => $item) {
    //         $product = $products[$productId] ?? null;
    //         if (!$product) continue;

    //         $quantity = $item['quantity'];
    //         $price = $product->price;

    //         foreach ($product->product_price as $tier) {
    //             if ($quantity >= $tier->min_quantity) {
    //                 $price = $tier->price;
    //             }
    //         }

    //         $subtotal = $price * $quantity;
    //         $total += $subtotal;

    //         $orderDetails[] = [
    //             'product_id' => $product->id,
    //             'price_at_order' => $price,
    //             'quantity' => $quantity,
    //             'subtotal' => $subtotal,
    //         ];
    //     }

    //     // Simpan order
    //     $order = new \App\Models\orders();
    //     $order->user_id = $user->id;
    //     $order->status_id = status::where('label', $paymentMethod === 'midtrans' ? 'pending' : 'processing')->first()->id;
    //     $order->total_price = $total;
    //     $order->payment_method = $paymentMethod;
    //     $order->purchase_type = $purchase_type;
    //     $order->address_id = $purchase_type === 'delivery' ? $addressId : null;
    //     $order->reference_number = 'ORD-' . strtoupper(Str::random(10));
    //     $order->save();

    //     // Setelah simpan order dan order_details
    //     foreach ($orderDetails as $detail) {
    //         $detail['order_id'] = $order->id;
    //         order_details::create($detail);
    //     }

    //     // Buat item_details untuk Midtrans
    //     $items = [];
    //     foreach ($order->order_detail as $detail) {
    //         $product = products::find($detail['product_id']);
    //         $items[] = [
    //             'id' => $detail['product_id'],
    //             'price' => $detail['price_at_order'],
    //             'quantity' => $detail['quantity'],
    //             'name' => Str::limit(preg_replace('/[^A-Za-z0-9 ]/', '', $product->name), 50),
    //         ];
    //     }

    //     $address = $order->address_id ? user_addresses::find($order->address_id) : null;

    //     $params = [
    //         'transaction_details' => [
    //             'order_id' => $order->reference_number,
    //             'gross_amount' => max($order->total_price, 100),
    //         ],
    //         'item_details' => $items,
    //         'customer_details' => [
    //             'first_name' => $user->name,
    //             'email' => $user->email,
    //             'shipping_address' => $address ? [
    //                 'first_name' => $address->name,
    //                 'phone' => $address->phone,
    //                 'address' => $address->address,
    //                 'city' => $address->city,
    //                 'postal_code' => $address->post_code,
    //                 'country_code' => 'IDN',
    //             ] : [],
    //         ],
    //     ];

    //     $snap = Snap::createTransaction($params);

    //     $order->snap_token = $snap->token;
    //     $order->snap_redirect_url = $snap->redirect_url; // Tambahkan kolom ini di tabel orders
    //     $order->save();

    //     // ✅ Kosongkan cart (opsional)
    //     // session()->forget('cart');


    //     $auth = [
    //         'VAPID' => [
    //             'subject' => env('VAPID_SUBJECT'),
    //             'publicKey' => env('VAPID_PUBLIC_KEY'),
    //             'privateKey' => env('VAPID_PRIVATE_KEY'),
    //         ]
    //     ];

    //     $webPush = new WebPush($auth);

    //     $payload = json_encode([
    //         'title' => 'Order Baru Masuk',
    //         'body' => 'Ada pesanan baru dari ' . $user->name,
    //         'url' => url('/admin') // URL admin ke halaman orders
    //     ]);

    //     foreach (PushSubscription::all() as $sub) {
    //         $webPush->sendOneNotification(
    //             Subscription::create($sub->subscription),
    //             $payload
    //         );
    //     }

    //     // ✅ Return Snap Token agar langsung bisa dipakai untuk memunculkan popup
    //     return response()->json([
    //         'success' => true,
    //         'snapToken' => $snap->token,
    //         'redirect_url' => $snap->redirect_url,
    //         'order_id' => $order->id,
    //     ]);

    // }

    public function store(Request $request)
    {
        $user = auth()->user();
        $cart = session('cart', []);

        if (empty($cart)) {
            return response()->json(['error' => 'Keranjang kosong']);
        }

        $purchase_type = $request->input('shipping_method');
        $paymentMethod = $request->input('payment_method');
        $addressId = $request->input('address_id');

        if ($purchase_type === 'delivery' && !$addressId) {
            return response()->json(['error' => 'Alamat pengiriman diperlukan']);
        }

        $ref = 'ORD-' . strtoupper(Str::random(10));

        // ✅ CASE 1: COD Pickup
        if ($purchase_type === 'pickup' && $paymentMethod === 'cod') {
            $total = 0;
            $orderDetails = [];

            $productIds = array_keys($cart);
            $products = products::with('product_price')->whereIn('id', $productIds)->get()->keyBy('id');

            foreach ($cart as $productId => $item) {
                $product = $products[$productId] ?? null;
                if (!$product) continue;

                $quantity = $item['quantity'];

                if ($product->quantity < $quantity) {
                    return response()->json(['error' => 'Stok tidak cukup untuk produk: '.$product->name], 400);
                }

                $product->quantity -= $quantity;
                $product->save();

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

            $order = new orders();
            $order->user_id = $user->id;
            $order->reference_number = $ref;
            $order->status_id = \App\Models\status::where('label', 'cod')->first()->id;
            $order->total_price = $total;
            $order->payment_method = $paymentMethod;
            $order->purchase_type = $purchase_type;
            $order->address_id = null;
            $order->save();

            foreach ($orderDetails as $detail) {
                $detail['order_id'] = $order->id;
                order_details::create($detail);
            }

            session()->forget('cart');

            // Notifikasi admin
            $auth = [
                'VAPID' => [
                    'subject' => env('VAPID_SUBJECT'),
                    'publicKey' => env('VAPID_PUBLIC_KEY'),
                    'privateKey' => env('VAPID_PRIVATE_KEY'),
                ]
            ];

            $webPush = new \Minishlink\WebPush\WebPush($auth);
            $payload = json_encode([
                'title' => 'Order Baru Masuk',
                'body' => 'Ada pesanan baru dari ' . $user->name,
                'url' => url('/admin'),
                'requireInteraction'=> true
            ]);

            foreach (\App\Models\PushSubscription::all() as $sub) {
                $webPush->sendOneNotification(
                    \Minishlink\WebPush\Subscription::create($sub->subscription),
                    $payload
                );
            }

            return response()->json(['order_ref' => $ref]);
        }

        // Cek dan kurangi stok
        foreach ($cart as $productId => $item) {
            $product = products::find($productId);
            $qty = $item['quantity'];

            if (!$product || $product->quantity < $qty) {
                return response()->json(['error' => "Stok tidak cukup untuk {$product->name}"], 400);
            }

            $product->quantity -= $qty;
            $product->save();
        }

        // Simpan ke temp_orders
        Log::info('Sebelum simpan temp_orders', ['ref' => $ref]);

        try {
            temp_orders::create([
                'user_id' => $user->id,
                'reference_number' => $ref,
                'cart' => json_encode($cart),
                'payment_method' => $paymentMethod,
                'purchase_type' => $purchase_type,
                'address_id' => $addressId,
            ]);
            Log::info('Berhasil simpan temp_orders', ['ref' => $ref]);
        } catch (\Exception $e) {
            Log::error('Gagal simpan temp_orders: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal simpan temp_orders'], 500);
        }



        session()->forget('cart');

        return response()->json(['order_ref' => $ref]);
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

        $products = products::with('product_price')
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

    // public function createSnapToken(Request $request)
    // {
    //     try {
    //         $order = orders::with('user', 'order_detail.product')
    //             ->where('user_id', auth()->id())
    //             ->where('id', $request->order_id)
    //             ->first();
    //         if (!$order) {
    //             return response()->json(['error' => 'Order tidak ditemukan'], 404);
    //         }

    //         $items = [];
    //         foreach ($order->order_detail as $detail) {
    //             $items[] = [
    //                 'id' => $detail->product_id,
    //                 'price' => $detail->price_at_order,
    //                 'quantity' => $detail->quantity,
    //                 'name' => Str::limit(preg_replace('/[^A-Za-z0-9 ]/', '', $detail->product->name), 50),
    //             ];
    //         }

    //         $address = $order->address_id ? \App\Models\user_addresses::find($order->address_id) : null;

    //         $params = [
    //             'transaction_details' => [
    //                 'order_id' => $order->reference_number,
    //                 'gross_amount' => max($order->total_price, 100),
    //             ],
    //             'item_details' => $items,
    //             'customer_details' => [
    //                 'first_name' => $order->user->name,
    //                 'email' => $order->user->email,
    //                 'shipping_address' => $address ? [
    //                     'first_name'   => $address->name,
    //                     'phone'        => $address->phone,
    //                     'address'      => $address->address,
    //                     'city'         => $address->city,
    //                     'postal_code'  => $address->post_code,
    //                     'country_code' => 'IDN',
    //                 ] : [],
    //             ],
    //         ];

    //         $snapToken = Snap::getSnapToken($params);

    //         return response()->json([
    //             'snapToken' => $snapToken,
    //             'order_id' => $order->id,
    //         ]);
    //     } catch (Exception $e) {
    //         return response()->json([
    //             'error' => true,
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function createSnapToken(Request $request)
    {
        $user = auth()->user();

        $tempOrder = \App\Models\temp_orders::where('reference_number', $request->order_ref)->first();

        if (!$tempOrder) {
            return response()->json(['error' => 'Order tidak ditemukan'], 404);
        }

        $cart = json_decode($tempOrder->cart, true);
        $productIds = array_keys($cart);
        $products = \App\Models\products::with('product_price')->whereIn('id', $productIds)->get()->keyBy('id');

        $items = [];
        $total = 0;

        foreach ($cart as $id => $item) {
            $product = $products[$id] ?? null;
            $qty = $item['quantity'];

            // ✅ Validasi produk ditemukan
            if (!$product) {
                return response()->json(['error' => "Produk ID $id tidak ditemukan"], 404);
            }

            // ✅ Hitung harga berdasarkan tier
            $price = $product->price;
            foreach ($product->product_price as $tier) {
                if ($qty >= $tier->min_quantity) {
                    $price = $tier->price;
                }
            }

            // ✅ Tambah ke item details Midtrans
            $items[] = [
                'id' => $product->id,
                'price' => $price,
                'quantity' => $qty,
                'name' => Str::limit(preg_replace('/[^A-Za-z0-9 ]/', '', $product->name), 50),
            ];

            $total += $price * $qty;
        }

        // ✅ Ambil alamat jika pengiriman
        $address = $tempOrder->purchase_type === 'delivery'
            ? \App\Models\user_addresses::find($tempOrder->address_id)
            : null;

        // ✅ Siapkan parameter Midtrans
        $params = [
            'transaction_details' => [
                'order_id' => $tempOrder->reference_number,
                'gross_amount' => max($total, 100), // Midtrans minimal 100
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
            'expiry' => [
            'start_time' => now()->format('Y-m-d H:i:s O'),
            'unit' => 'minute',
            'duration' => 15
            ]
        ];

        // ✅ Dapatkan Snap Token dari Midtrans
        $snapToken = \Midtrans\Snap::getSnapToken($params);

        return response()->json([
            'snapToken' => $snapToken,
            'order_ref' => $tempOrder->reference_number
        ]);
    }




    public function handleNotification(Request $request)
    {
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

        try {
            $notif = new \Midtrans\Notification();
            $transaction = $notif->transaction_status;
            $orderId = $notif->order_id;

            $existingOrder = \App\Models\orders::where('reference_number', $orderId)->first();

            if ($existingOrder) {
                if ($transaction === 'capture' || $transaction === 'settlement') {
                    $existingOrder->status_id = \App\Models\status::where('label', 'paid')->first()->id;
                } elseif ($transaction === 'pending') {
                    $existingOrder->status_id = \App\Models\status::where('label', 'pending')->first()->id;
                } elseif (in_array($transaction, ['deny', 'cancel', 'expire'])) {
                    $existingOrder->status_id = \App\Models\status::where('label', 'failed')->first()->id;
                }
                $existingOrder->save();

                return response()->json(['message' => 'Order status updated']);
            }

            $temp = null;
            $retry = 0;
            while (!$temp && $retry < 5) {
                $temp = \App\Models\temp_orders::where('reference_number', $orderId)->first();
                if (!$temp) {
                    usleep(200000); // tunggu 200ms
                    $retry++;
                }
            }

            if (!$temp) {
                Log::warning('Temp order not found setelah 5x retry', ['order_id' => $orderId]);
                return response()->json(['error' => 'Temp order not found'], 404);
            }


            if (!$temp) {
                Log::warning('Temp order not found', ['order_id' => $orderId]);
                return response()->json(['error' => 'Temp order not found'], 404);
            }

            $user = \App\Models\User::find($temp->user_id);
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            $cart = json_decode($temp->cart, true);
            $total = 0;
            $orderDetails = [];

            $productIds = array_keys($cart);
            $products = \App\Models\products::with('product_price')->whereIn('id', $productIds)->get()->keyBy('id');

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

            $order = new \App\Models\orders();
            $order->user_id = $user->id;
            $order->reference_number = $orderId;
            $order->status_id = \App\Models\status::where('label', 'paid')->first()->id;
            $order->total_price = $total;
            $order->payment_method = $temp->payment_method;
            $order->purchase_type = $temp->purchase_type;
            $order->address_id = $temp->purchase_type === 'delivery' ? $temp->address_id : null;
            $order->save();

            foreach ($orderDetails as $detail) {
                $detail['order_id'] = $order->id;
                \App\Models\order_details::create($detail);
            }

            $auth = [
                'VAPID' => [
                    'subject' => env('VAPID_SUBJECT'),
                    'publicKey' => env('VAPID_PUBLIC_KEY'),
                    'privateKey' => env('VAPID_PRIVATE_KEY'),
                ]
            ];

            $webPush = new \Minishlink\WebPush\WebPush($auth);
            $payload = json_encode([
                'title' => 'Order Baru Masuk',
                'body' => 'Ada pesanan baru dari ' . $user->name,
                'url' => url('/admin')
            ]);

            foreach (\App\Models\PushSubscription::all() as $sub) {
                $webPush->sendOneNotification(
                    \Minishlink\WebPush\Subscription::create($sub->subscription),
                    $payload
                );
            }

            // Hapus dari temp_orders
            $temp->delete();

            return response()->json(['message' => 'Order berhasil disimpan']);
        } catch (\Exception $e) {
            Log::error('Midtrans Notification Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function paymentCancelled(Request $request)
    {
        $orderRef = $request->input('order_ref');

        $temp = temp_orders::where('reference_number', $orderRef)->first();

        if (!$temp) {
            return response()->json(['message' => 'Order tidak ditemukan'], 404);
        }

        // Rollback stok
        $cart = json_decode($temp->cart, true);
        foreach ($cart as $productId => $item) {
            $product = products::find($productId);
            if ($product) {
                $product->quantity += $item['quantity'];
                $product->save();
            }
        }

        // Hapus temp_order 
        $temp->delete();

        Log::info("Temp order {$orderRef} dibatalkan manual via Snap onClose. Stok dikembalikan.");

        return response()->json(['message' => 'Order dibatalkan dan stok dikembalikan']);
    }

}
