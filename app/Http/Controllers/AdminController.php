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
            ->whereIn('status_id', [12, 2]) //cod paid
            ->count();

        $deliveryCount = orders::where('purchase_type', 'delivery')
            ->where('status_id', 2) //paid
            ->count();

        $completedCount = orders::whereIn('status_id', [4,5])->count(); //delivered completed

        $totalCount = $pickupCount + $deliveryCount;

        $pickupAmount = orders::where('purchase_type', 'pickup')
            ->whereIn('status_id', [12, 2]) //cod paid
            ->sum('total_price');

        $deliveryAmount = orders::where('purchase_type', 'delivery')
            ->where('status_id', 2) //paid
            ->sum('total_price');

        $completedAmount = orders::whereIn('status_id', [4,5]) //delivered completed
        ->sum('total_price');

        $totalAmount = $pickupAmount + $deliveryAmount;

        
        $ongoingCount = orders::where('purchase_type', 'delivery')
            ->whereIn('status_id', [10, 11, 3, 4]) //ongoing
            ->count();

        $ongoingAmount = orders::where('purchase_type', 'delivery')
            ->whereIn('status_id', [10, 11, 3, 4]) //ongoing
            ->sum('total_price');


        $type = $request->input('type'); // pickup / delivery / null

        $ordersQuery = orders::with(['user.user_address', 'order_detail', 'status'])->latest();

        if ($type === 'pickup') {
            $ordersQuery->where('purchase_type', 'pickup')
                        ->whereIn('status_id', [2,12]); //paid cod
            // Semua status untuk pickup
        } elseif ($type === 'delivery') {
            $ordersQuery->where('purchase_type', 'delivery')
                        ->where('status_id', 2); // paid
        } else {
            // Jika tanpa filter (All), ambil pickup semua status + delivery yang paid
            $ordersQuery->where(function ($query) {
                $query->where('purchase_type', 'pickup')
                    ->whereIn('status_id', [12,2]) //cod paid
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('purchase_type', 'delivery')
                                ->where('status_id', 2); //cod
                    });
            });
        }

        $orders = $ordersQuery->paginate(10);

        return view("dashboard.dashboard", compact('pickupCount','deliveryCount','ongoingAmount','ongoingCount','totalCount','orders','totalAmount','pickupAmount','deliveryAmount','completedCount','completedAmount'));
    }

    public function show($id)
    {
        $order = orders::with([
            'user',
            'address', // ambil alamat yang dipakai di order
            'order_detail.product',
            'status'
        ])->findOrFail($id);

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
            ->whereIn('status_id', [4,5]); //delivered completed


        $orders = $ordersQuery->paginate(10);

        return view("dashboard.order-completed", compact('orders'));
    }

    public function index_ready(Request $request)
    {
        $type = $request->input('type'); // pickup / delivery / null

        $ordersQuery = orders::with(['user.user_address', 'order_detail', 'status'])
            ->latest()
            ->whereIn('status_id', [10,11]); //ready to pick up ready to send


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
                        ->whereIn('status_id', [1,2]); // pending paid
            // Semua status untuk pickup
        } elseif ($type === 'delivery') {
            $ordersQuery->where('purchase_type', 'delivery')
                        ->where('status_id', 2); // paid
        } else {
            // Jika tanpa filter (All), ambil pickup semua status + delivery yang paid
            $ordersQuery->where(function ($query) {
                $query->where('purchase_type', 'pickup')
                    ->whereIn('status_id', [1,2]) // pending paid
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('purchase_type', 'delivery')
                                ->where('status_id', 2); //paid
                    });
            });
        }

        $orders = $ordersQuery->paginate(10);

        return view("dashboard.order-unprocessed", compact('orders'));
    }
    
}
