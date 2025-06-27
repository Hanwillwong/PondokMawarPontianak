<?php

namespace App\Http\Controllers;

use App\Models\account;
use App\Models\user_addresses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\orders;
use Midtrans\Transaction;
use Illuminate\Support\Facades\Log;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("pages.account");
    }

    public function index_address()
    {
        $addresses = user_addresses::where('user_id', Auth::id())->get();
        return view("pages.account-address", compact('addresses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    public function create_address()
    {
        return view("pages.account-address-add");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    public function store_address(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'province' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'address' => 'required|string',
            'post_code' => 'required|string|max:10',
        ]);

        user_addresses::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'phone' => $request->phone,
            'province' => $request->province,
            'city' => $request->city,
            'address' => $request->address,
            'post_code' => $request->post_code,
        ]);
        
        return redirect($request->input('redirect_to', route('pages.account-address')))
        ->with('success', 'Alamat berhasil disimpan!');
    }
    
    /**
     * Display the specified resource.
     */
    public function show(account $account)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(account $account)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, account $account)
    {
        
    }

    public function edit_address($id)
    {
        $address = user_addresses::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('pages.account-address-edit', compact('address'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update_address(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'province' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'address' => 'required|string|max:500',
            'post_code' => 'required|string|max:20',
        ]);

        $address = user_addresses::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $address->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'province' => $request->province,
            'city' => $request->city,
            'address' => $request->address,
            'post_code' => $request->post_code,
        ]);

        return redirect()->route('pages.account-address')->with('success', 'Alamat berhasil diperbarui.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(account $account)
    {
        //
    }

    public function orders()
    {
        $orders = orders::with('order_detail.product', 'status')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('pages.account-orders', compact('orders'));
    }

    public function orders_details($id)
    {
        $order = orders::with('order_detail.product', 'status', 'address')
            ->where('user_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();

        $midtrans = null;

        $expiredTime = $order->created_at->addHour();

        if ($order->payment_method === 'midtrans') {
            try {
                \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
                \Midtrans\Config::$isProduction = false;
                \Midtrans\Config::$isSanitized = true;
                \Midtrans\Config::$is3ds = true;

                $midtrans = \Midtrans\Transaction::status($order->reference_number);
            } catch (\Exception $e) {
                Log::error('Midtrans error: ' . $e->getMessage());
                $midtrans = null;
            }
        }

        return view('pages.account-order-details', compact('order', 'midtrans','expiredTime'));
    }

}
