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
    public function handleNotification(Request $request)
    {
        Log::info('Webhook DITERIMA', [
            'raw' => file_get_contents('php://input'),
            'parsed' => $request->all(),
        ]);

        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        try {
            $notif = new Notification();
            Log::info('Notification dari Midtrans', [
                'transaction_status' => $notif->transaction_status,
                'order_id' => $notif->order_id,
            ]);

            $transaction = $notif->transaction_status;
            $orderId = $notif->order_id;

            $order = orders::where('reference_number', $orderId)->first();

            // 👉 Tambahkan di sini:
            if (!$order) {
                Log::warning('Order tidak ditemukan dari order_id Midtrans', ['order_id' => $orderId]);
                return response()->json(['error' => 'Order tidak ditemukan'], 404);
            }

            // Proses status pembayaran
            if ($transaction === 'capture' || $transaction === 'settlement') {
                $order->status_id = status::where('label', 'paid')->first()->id;
            } elseif ($transaction === 'pending') {
                $order->status_id = status::where('label', 'pending')->first()->id;
            } elseif (in_array($transaction, ['deny', 'cancel', 'expire'])) {
                $order->status_id = status::where('label', 'failed')->first()->id;
            }

            $order->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Midtrans Notification Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
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
