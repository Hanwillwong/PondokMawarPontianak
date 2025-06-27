<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\orders;
use App\Models\status;
use Midtrans\Notification;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Illuminate\Support\Str;
use Midtrans\Snap;


class MidtransController extends Controller
{
    // public function handleNotification(Request $request)
    // {
    //     Log::info('Webhook DITERIMA', [
    //         'raw' => file_get_contents('php://input'),
    //         'parsed' => $request->all(),
    //     ]);

    //     Config::$serverKey = config('midtrans.server_key');
    //     Config::$isProduction = config('midtrans.is_production');
    //     Config::$isSanitized = config('midtrans.is_sanitized');
    //     Config::$is3ds = config('midtrans.is_3ds');

    //     try {
    //         $notif = new Notification();
    //         Log::info('Notification dari Midtrans', [
    //             'transaction_status' => $notif->transaction_status,
    //             'order_id' => $notif->order_id,
    //         ]);

    //         $transaction = $notif->transaction_status;
    //         $orderId = $notif->order_id;

    //         $order = orders::where('reference_number', $orderId)->first();

    //         // 👉 Tambahkan di sini:
    //         if (!$order) {
    //             Log::warning('Order tidak ditemukan dari order_id Midtrans', ['order_id' => $orderId]);
    //             return response()->json(['error' => 'Order tidak ditemukan'], 404);
    //         }

    //         // Proses status pembayaran
    //         if ($transaction === 'capture' || $transaction === 'settlement') {
    //             $order->status_id = status::where('label', 'paid')->first()->id;
    //         } elseif ($transaction === 'pending') {
    //             $order->status_id = status::where('label', 'pending')->first()->id;
    //         } elseif (in_array($transaction, ['deny', 'cancel', 'expire'])) {
    //             $order->status_id = status::where('label', 'failed')->first()->id;
    //         }

    //         $order->save();

    //         return response()->json(['success' => true]);
    //     } catch (\Exception $e) {
    //         Log::error('Midtrans Notification Error: ' . $e->getMessage());
    //         return response()->json(['error' => 'Internal Server Error'], 500);
    //     }
    // }

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

            $existingOrder = \App\Models\orders::where('reference_number', $orderId)->first();

            if ($existingOrder) {
                $newStatus = null;

                if ($transaction === 'capture') {
                    $newStatus = $notif->fraud_status === 'challenge' ? 'pending' : 'paid';
                } elseif ($transaction === 'settlement') {
                    $newStatus = 'paid';
                } elseif ($transaction === 'pending') {
                    $newStatus = 'pending';
                } elseif (in_array($transaction, ['deny', 'cancel', 'expire'])) {
                    $newStatus = 'failed';
                }

                if ($newStatus) {
                    $statusId = \App\Models\status::where('label', $newStatus)->value('id');

                    if ($statusId && $existingOrder->status_id !== $statusId) {
                        $existingOrder->status_id = $statusId;
                        $existingOrder->save();

                        Log::info("Order #{$orderId} status updated to {$newStatus}");
                    }
                }

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

                // ✅ CEK STOK
                if ($product->quantity < $quantity) {
                    Log::warning("Stok tidak cukup untuk produk {$product->name}");
                    return response()->json(['error' => 'Stok tidak cukup untuk produk: '.$product->name], 400);
                }

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

            $status = 'pending'; // default

            if ($transaction === 'capture' || $transaction === 'settlement') {
                $status = 'paid';
            } elseif ($transaction === 'pending') {
                $status = 'pending';
            } elseif (in_array($transaction, ['deny', 'cancel', 'expire'])) {
                $status = 'failed';
            }

            $order->status_id = \App\Models\status::where('label', $status)->first()->id;

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
                'url' => url('/admin'),
                'requireInteraction'=> true
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

    public function regenerateSnapToken(Request $request)
    {
        try {
            $oldOrder = orders::with('user', 'order_detail.product')
                ->where('user_id', auth()->id())
                ->where('id', $request->order_id)
                ->whereHas('status', function ($q) {
                    $q->where('label', 'pending');
                })
                ->first();

            if (!$oldOrder) {
                return response()->json(['error' => 'Order tidak ditemukan atau tidak bisa diulang'], 404);
            }

            // 👉 Duplikasi order lama ke order baru
            $newOrder = $oldOrder->replicate();
            $newOrder->reference_number = 'ORD-' . strtoupper(Str::random(10));
            $newOrder->snap_token = null;
            $newOrder->redirect_url = null;
            $newOrder->created_at = now();
            $newOrder->updated_at = now();
            $newOrder->save();

            // Duplikasikan order detail
            foreach ($oldOrder->order_detail as $detail) {
                $newOrder->order_detail()->create([
                    'product_id' => $detail->product_id,
                    'price_at_order' => $detail->price_at_order,
                    'quantity' => $detail->quantity,
                    'subtotal' => $detail->subtotal,
                ]);
            }

            // 🔁 Buat ulang SnapToken
            $items = [];
            foreach ($newOrder->order_detail as $detail) {
                $items[] = [
                    'id' => $detail->product_id,
                    'price' => $detail->price_at_order,
                    'quantity' => $detail->quantity,
                    'name' => Str::limit(preg_replace('/[^A-Za-z0-9 ]/', '', $detail->product->name), 50),
                ];
            }

            $address = $newOrder->address_id ? \App\Models\user_addresses::find($newOrder->address_id) : null;

            $params = [
                'transaction_details' => [
                    'order_id' => $newOrder->reference_number,
                    'gross_amount' => max($newOrder->total_price, 100),
                ],
                'item_details' => $items,
                'customer_details' => [
                    'first_name' => $newOrder->user->name,
                    'email' => $newOrder->user->email,
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

            // Simpan SnapToken baru
            $newOrder->snap_token = $snapToken;
            $newOrder->save();

            return response()->json([
                'snapToken' => $snapToken,
                'order_id' => $newOrder->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }





}
