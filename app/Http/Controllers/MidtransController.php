<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\orders;
use App\Models\status;
use Midtrans\Notification;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;

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

            $order = Orders::where('reference_number', $orderId)->first();

            // 👉 Tambahkan di sini:
            if (!$order) {
                Log::warning('Order tidak ditemukan dari order_id Midtrans', ['order_id' => $orderId]);
                return response()->json(['error' => 'Order tidak ditemukan'], 404);
            }

            // Proses status pembayaran
            if ($transaction === 'capture' || $transaction === 'settlement') {
                $order->status_id = Status::where('label', 'paid')->first()->id;
            } elseif ($transaction === 'pending') {
                $order->status_id = Status::where('label', 'pending')->first()->id;
            } elseif (in_array($transaction, ['deny', 'cancel', 'expire'])) {
                $order->status_id = Status::where('label', 'failed')->first()->id;
            }

            $order->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Midtrans Notification Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

}
