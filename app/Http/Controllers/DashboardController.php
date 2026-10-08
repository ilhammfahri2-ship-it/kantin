<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $orderQuery = Order::with(['items', 'tenant'])
            ->orderBy('created_at', 'desc');

        $productQuery = Product::with('tenant')->latest();
        $ingredientQuery = Ingredient::with('tenant')->latest();

        // Jika user adalah tenant, hanya tampilkan pesanan, produk & bahan untuk tenant miliknya
        if ($user && $user->isTenant() && $user->tenant) {
            $orderQuery->where('tenant_id', $user->tenant->id);
            $productQuery->where('tenant_id', $user->tenant->id);
            $ingredientQuery->where('tenant_id', $user->tenant->id);
        }

        $orders = $orderQuery->get();
        $products = $productQuery->get();
        $ingredients = $ingredientQuery->get();

        // Hitung ringkasan statistik pesanan
        $totalRevenue = $orders->where('status', 'completed')->sum('total');
        $activeOrdersCount = $orders->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready'])->count();
        $completedOrdersCount = $orders->where('status', 'completed')->count();
        $pendingOrdersCount = $orders->where('status', 'pending')->count();

        // Ringkasan statistik produk & stok porsi
        $totalProductsCount = $products->count();
        $outOfStockCount = $products->where('stock', '<=', 0)->count();
        $lowStockCount = $products->where('stock', '>', 0)->where('stock', '<=', 5)->count();

        // Ringkasan statistik bahan baku kantin
        $totalIngredientsCount = $ingredients->count();
        $outOfStockIngredientsCount = $ingredients->filter->is_out_of_stock->count();
        $lowStockIngredientsCount = $ingredients->filter->is_low_stock->count();

        $tenants = Tenant::all();

        return view('dashboard.index', compact(
            'orders',
            'products',
            'ingredients',
            'totalRevenue',
            'activeOrdersCount',
            'completedOrdersCount',
            'pendingOrdersCount',
            'totalProductsCount',
            'outOfStockCount',
            'lowStockCount',
            'totalIngredientsCount',
            'outOfStockIngredientsCount',
            'lowStockIngredientsCount',
            'tenants'
        ));
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
