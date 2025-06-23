<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\orders;
use App\Models\status;

use App\Models\user_addresses;

class AdminController extends Controller
{
    public function index(Request $request)
    {

        $pickupCount = orders::where('purchase_type', 'pickup')
            ->whereIn('status_id', [1, 2])
            ->count();

        $deliveryCount = orders::where('purchase_type', 'delivery')
            ->where('status_id', 2)
            ->count();

        $completedCount = orders::whereIn('status_id', [4,5])->count();

        $totalCount = $pickupCount + $deliveryCount;

        $pickupAmount = orders::where('purchase_type', 'pickup')
            ->whereIn('status_id', [1, 2])
            ->sum('total_price');

        $deliveryAmount = orders::where('purchase_type', 'delivery')
            ->where('status_id', 2)
            ->sum('total_price');

        $completedAmount = orders::whereIn('status_id', [4,5])
        ->sum('total_price');

        $totalAmount = $pickupAmount + $deliveryAmount;


        $type = $request->input('type'); // pickup / delivery / null

        $ordersQuery = orders::with(['user.user_address', 'order_detail', 'status'])->latest();

        if ($type === 'pickup') {
            $ordersQuery->where('purchase_type', 'pickup')
                        ->whereIn('status_id', [2,1]);
            // Semua status untuk pickup
        } elseif ($type === 'delivery') {
            $ordersQuery->where('purchase_type', 'delivery')
                        ->where('status_id', 2); // Hanya yang sudah dibayar
        } else {
            // Jika tanpa filter (All), ambil pickup semua status + delivery yang paid
            $ordersQuery->where(function ($query) {
                $query->where('purchase_type', 'pickup')
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('purchase_type', 'delivery')
                                ->where('status_id', 2);
                    });
            });
        }

        $orders = $ordersQuery->paginate(10);

        return view("dashboard.dashboard", compact('pickupCount','deliveryCount','totalCount','orders','totalAmount','pickupAmount','deliveryAmount','completedCount','completedAmount'));
    }

    public function show($id)
    {
        $order = orders::with(['user.user_address', 'order_detail.product', 'status'])->findOrFail($id);
        $statuses = status::all(); // untuk dropdown ubah status
        return view('dashboard.order-detail', compact('order', 'statuses'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_id' => 'required|exists:statuses,id'
        ]);

        $order = orders::findOrFail($id);
        $order->status_id = $request->input('status_id');
        $order->save();

        return redirect()->back()->with('success', 'Order status updated successfully.');
    }

    public function index_completed(Request $request)
    {
        $type = $request->input('type'); // pickup / delivery / null

        $ordersQuery = orders::with(['user.user_address', 'order_detail', 'status'])
            ->latest()
            ->whereIn('status_id', [4,5]);


        $orders = $ordersQuery->paginate(10);

        return view("dashboard.order-completed", compact('orders'));
    }

    public function index_ready(Request $request)
    {
        $type = $request->input('type'); // pickup / delivery / null

        $ordersQuery = orders::with(['user.user_address', 'order_detail', 'status'])
            ->latest()
            ->whereIn('status_id', [10,11]);


        $orders = $ordersQuery->paginate(10);

        return view("dashboard.order-ready", compact('orders'));
    }

    public function index_unprocessed(Request $request)
    {
        $type = $request->input('type'); // pickup / delivery / null

        $ordersQuery = orders::with(['user.user_address', 'order_detail', 'status'])
            ->latest();

        if ($type === 'pickup') {
            $ordersQuery->where('purchase_type', 'pickup')
                        ->whereIn('status_id', [1,2]);
            // Semua status untuk pickup
        } elseif ($type === 'delivery') {
            $ordersQuery->where('purchase_type', 'delivery')
                        ->where('status_id', 2); // Hanya yang sudah dibayar
        } else {
            // Jika tanpa filter (All), ambil pickup semua status + delivery yang paid
            $ordersQuery->where(function ($query) {
                $query->where('purchase_type', 'pickup')
                    ->whereIn('status_id', [1,2])
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('purchase_type', 'delivery')
                                ->where('status_id', 2);
                    });
            });
        }

        $orders = $ordersQuery->paginate(10);

        return view("dashboard.order-unprocessed", compact('orders'));
    }
    
}
