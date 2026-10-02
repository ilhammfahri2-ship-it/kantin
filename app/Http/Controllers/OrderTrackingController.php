<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan login untuk melihat pesanan Anda.');
        }

        $orders = Order::where('user_id', auth()->id())
            ->with(['tenant', 'items'])
            ->latest()
            ->get();

        return view('orders.index', compact('orders'));
    }
}
