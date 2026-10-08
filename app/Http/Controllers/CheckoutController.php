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
            'tenant_id' => 'required|exists:tenants,id',
            'customer_name' => 'required|string|max:255',
            'customer_class' => 'required|string|max:50',
            'payment_method' => 'required|in:cash,qris',
        ]);

        $userId = auth()->id();

        DB::beginTransaction();
        try {
            $subtotal = 0;
            $orderItems = [];

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

                $itemSubtotal = $product->price * $itemData['quantity'];
                $subtotal += $itemSubtotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $itemData['quantity'],
                    'subtotal' => $itemSubtotal,
                ];
            }

            // Hitung diskon voucher jika ada
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
                        $discount = round(($subtotal * $voucher->discount_percent) / 100);
                        $voucherCode = $voucher->code;
                    }
                }
            }

            $total = max(0, $subtotal - $discount);

            // Create Order
            $order = Order::create([
                'user_id' => $userId,
                'tenant_id' => $request->tenant_id,
                'customer_name' => $request->customer_name,
                'customer_class' => $request->customer_class,
                'order_number' => Order::generateOrderNumber(),
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'payment_method' => $request->payment_method,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $total,
                'voucher_code' => $voucherCode,
                'notes' => $request->notes,
            ]);

            // Save Order Items
            foreach ($orderItems as $orderItem) {
                $orderItem['order_id'] = $order->id;
                OrderItem::create($orderItem);
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
            return response()->json([
                'success' => true, 
                'message' => 'Pesanan berhasil dibuat!', 
                'redirect_url' => route('checkout.success', $order->id)
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
