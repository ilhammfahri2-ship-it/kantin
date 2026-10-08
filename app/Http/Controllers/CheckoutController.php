<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Services\CanteenSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function store(Request $request)
    {
        // Validasi jam operasional kantin (Istirahat 1: 09:30 - 10:00 WIB & Istirahat 2: 12:00 - 13:00 WIB)
        if (!CanteenSchedule::isOpen()) {
            return response()->json([
                'success' => false,
                'message' => 'Kantin sedang tutup. Pemesanan hanya dibuka saat jam istirahat: Sesi 1 (09:30 - 10:00 WIB) dan Sesi 2 (12:00 - 13:00 WIB).'
            ], 400);
        }

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'customer_name' => 'required|string|max:255',
            'customer_class' => 'required|string|max:50',
            'payment_method' => 'required|in:cash,qris',
        ]);

        $userId = auth()->id();

        DB::beginTransaction();
        try {
            // Group request items by tenant_id
            $itemsByTenant = [];
            foreach ($request->items as $itemData) {
                $product = Product::findOrFail($itemData['id']);
                
                if ($product->stock < $itemData['quantity']) {
                    return response()->json([
                        'success' => false, 
                        'message' => "Stok produk {$product->name} tidak mencukupi. Sisa stok: {$product->stock}"
                    ], 400);
                }
                
                // Reduce stock
                $product->decrement('stock', $itemData['quantity']);

                $tenantId = $product->tenant_id;
                if (!isset($itemsByTenant[$tenantId])) {
                    $itemsByTenant[$tenantId] = [
                        'subtotal' => 0,
                        'orderItems' => []
                    ];
                }

                $itemSubtotal = $product->price * $itemData['quantity'];
                $itemsByTenant[$tenantId]['subtotal'] += $itemSubtotal;

                $itemsByTenant[$tenantId]['orderItems'][] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $itemData['quantity'],
                    'subtotal' => $itemSubtotal,
                ];
            }

            // Hitung diskon voucher jika ada
            $totalSubtotal = array_sum(array_column($itemsByTenant, 'subtotal'));
            $discount = 0;
            $voucherCode = null;
            $voucherClaim = null;

            $activeVoucherData = session('active_voucher');
            $candidateCode = $request->input('voucher_code', $activeVoucherData['code'] ?? null);

            if ($candidateCode && $userId) {
                $voucher = \App\Models\Voucher::where('code', $candidateCode)->first();
                if ($voucher && $voucher->isValid()) {
                    $voucherClaim = \App\Models\VoucherClaim::where('voucher_id', $voucher->id)
                        ->where('user_id', $userId)
                        ->where('is_used', false)
                        ->first();

                    if ($voucherClaim) {
                        $discount = round(($totalSubtotal * $voucher->discount_percent) / 100);
                        $voucherCode = $voucher->code;
                    }
                }
            }

            // Create an order for each tenant
            $createdOrders = [];
            foreach ($itemsByTenant as $tenantId => $tenantData) {
                $tenantTotal = max(0, $tenantData['subtotal'] - $discount);
                $order = Order::create([
                    'user_id' => $userId,
                    'tenant_id' => $tenantId,
                    'customer_name' => $request->customer_name,
                    'customer_class' => $request->customer_class,
                    'order_number' => 'ORD-' . date('Ymd') . '-' . strtoupper(uniqid()),
                    'status' => 'pending',
                    'payment_status' => 'unpaid',
                    'payment_method' => $request->payment_method,
                    'subtotal' => $tenantData['subtotal'],
                    'discount' => $discount,
                    'total' => $tenantTotal,
                    'voucher_code' => $voucherCode,
                    'notes' => $request->notes,
                ]);

                foreach ($tenantData['orderItems'] as $orderItem) {
                    $orderItem['order_id'] = $order->id;
                    OrderItem::create($orderItem);
                }
                
                $createdOrders[] = $order;
            }

            // Tandai voucher claim sebagai sudah digunakan & hubungkan ke order
            if ($voucherClaim) {
                $voucherClaim->update([
                    'is_used' => true,
                    'order_id' => $order->id,
                ]);
                session()->forget('active_voucher');
            }

            DB::commit();
            
            // If only 1 order, redirect to success page. If multiple, redirect to orders list.
            $redirectUrl = count($createdOrders) === 1 
                ? route('checkout.success', $createdOrders[0]->id)
                : route('orders.index');

            return response()->json([
                'success' => true, 
                'message' => 'Pesanan berhasil dibuat!', 
                'redirect_url' => $redirectUrl
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false, 
                'message' => 'Terjadi kesalahan saat memproses pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function success(Order $order)
    {
        // Pastikan relasi order item ter-load
        $order->load(['items', 'tenant']);
        return view('cart.success', compact('order'));
    }
}
