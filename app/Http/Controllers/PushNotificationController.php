<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PushSubscription;

class PushNotificationController extends Controller
{
    public function saveSubscription(Request $request)
    {
        // bisa diberi filter is_admin jika perlu
        PushSubscription::create([
            'subscription' => json_decode($request->sub, true),
        ]);

        return response()->json(['message' => 'Subscription saved']);
    }
}
