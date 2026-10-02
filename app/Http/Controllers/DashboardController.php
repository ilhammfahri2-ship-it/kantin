<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $query = Order::with(['items', 'tenant'])
            ->orderBy('created_at', 'desc');

        // Jika user adalah tenant, hanya tampilkan pesanan untuk tenant (warung) miliknya
        if ($user && $user->isTenant() && $user->tenant) {
            $query->where('tenant_id', $user->tenant->id);
        }

        $orders = $query->get();

        // Hitung ringkasan statistik
        $totalRevenue = $orders->where('status', 'completed')->sum('total');
        $activeOrdersCount = $orders->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready'])->count();
        $completedOrdersCount = $orders->where('status', 'completed')->count();
        $pendingOrdersCount = $orders->where('status', 'pending')->count();

        return view('dashboard.index', compact('orders', 'totalRevenue', 'activeOrdersCount', 'completedOrdersCount', 'pendingOrdersCount'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,completed,cancelled'
        ]);

        $order->status = $request->status;
        
        if ($request->status === 'completed') {
            $order->completed_at = now();
            // Asumsi pesanan selesai = sudah dibayar
            if ($order->payment_method === 'cash') {
                $order->payment_status = 'paid';
            }
        }

        $order->save();

        return back()->with('success', "Status pesanan {$order->order_number} berhasil diperbarui menjadi {$order->status_label}.");
    }
}
