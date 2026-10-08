<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    /**
     * Halaman Utama: Manajemen Stok Bahan & Produk
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $ingredientQuery = Ingredient::with('tenant')->latest();
        $productQuery = Product::with('tenant')->latest();

        if ($user && $user->isTenant() && $user->tenant) {
            $ingredientQuery->where('tenant_id', $user->tenant->id);
            $productQuery->where('tenant_id', $user->tenant->id);
        }

        $ingredients = $ingredientQuery->get();
        $products = $productQuery->get();

        // Metrik Bahan
        $totalIngredientsCount = $ingredients->count();
        $outOfStockIngredientsCount = $ingredients->filter->is_out_of_stock->count();
        $lowStockIngredientsCount = $ingredients->filter->is_low_stock->count();

        // Metrik Produk
        $totalProductsCount = $products->count();
        $outOfStockProductsCount = $products->where('stock', '<=', 0)->count();

        $tenants = Tenant::all();

        return view('dashboard.stock.index', compact(
            'ingredients',
            'products',
            'totalIngredientsCount',
            'outOfStockIngredientsCount',
            'lowStockIngredientsCount',
            'totalProductsCount',
            'outOfStockProductsCount',
            'tenants'
        ));
    }

    /**
     * Tambah Bahan Baku Baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'category'      => 'required|string',
            'stock'         => 'required|numeric|min:0',
            'unit'          => 'required|string|max:20',
            'min_stock'     => 'required|numeric|min:0',
            'cost_per_unit' => 'nullable|numeric|min:0',
            'supplier'      => 'nullable|string|max:255',
            'notes'         => 'nullable|string',
        ]);

        $data = $request->all();

        // Tetapkan tenant_id
        if (auth()->user()->isTenant() && auth()->user()->tenant) {
            $data['tenant_id'] = auth()->user()->tenant->id;
        } elseif (empty($data['tenant_id'])) {
            $data['tenant_id'] = Tenant::first()?->id ?? 1;
        }

        $ingredient = Ingredient::create($data);

        $redirectRoute = $request->input('redirect_to', 'dashboard.stock.index');
        return redirect()->route($redirectRoute, ['tab' => 'ingredients'])
            ->with('success', "Bahan baku '{$ingredient->name}' berhasil ditambahkan ke inventaris.");
    }

    /**
     * Penyesuaian / Restock Cepat Bahan Baku (+ Masuk, - Pemakaian, atau Koreksi)
     */
    public function adjust(Request $request, Ingredient $ingredient)
    {
        $user = auth()->user();
        if ($user->isTenant() && $user->tenant && $ingredient->tenant_id !== $user->tenant->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'type'   => 'required|in:in,out,set',
            'amount' => 'required|numeric|min:0',
        ]);

        $amount = (float) $request->amount;
        $oldStock = (float) $ingredient->stock;

        if ($request->type === 'in') {
            $ingredient->stock = $oldStock + $amount;
            $msg = "Stok '{$ingredient->name}' berhasil ditambah +{$amount} {$ingredient->unit} (Total: {$ingredient->formatted_stock}).";
        } elseif ($request->type === 'out') {
            $ingredient->stock = max(0, $oldStock - $amount);
            $msg = "Pemakaian '{$ingredient->name}' sebanyak -{$amount} {$ingredient->unit} berhasil dicatat (Sisa: {$ingredient->formatted_stock}).";
        } else {
            $ingredient->stock = max(0, $amount);
            $msg = "Stok fisik '{$ingredient->name}' berhasil diperbarui menjadi {$ingredient->formatted_stock}.";
        }

        $ingredient->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'ingredient' => [
                    'id'              => $ingredient->id,
                    'stock'           => $ingredient->stock,
                    'formatted_stock' => $ingredient->formatted_stock,
                    'status_label'    => $ingredient->status_label,
                    'is_out_of_stock' => $ingredient->is_out_of_stock,
                    'is_low_stock'    => $ingredient->is_low_stock,
                ]
            ]);
        }

        $redirectRoute = $request->input('redirect_to', 'dashboard.stock.index');
        return redirect()->route($redirectRoute, ['tab' => 'ingredients'])->with('success', $msg);
    }

    /**
     * Update Detail Bahan Baku
     */
    public function update(Request $request, Ingredient $ingredient)
    {
        $user = auth()->user();
        if ($user->isTenant() && $user->tenant && $ingredient->tenant_id !== $user->tenant->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name'          => 'required|string|max:255',
            'category'      => 'required|string',
            'stock'         => 'required|numeric|min:0',
            'unit'          => 'required|string|max:20',
            'min_stock'     => 'required|numeric|min:0',
            'cost_per_unit' => 'nullable|numeric|min:0',
            'supplier'      => 'nullable|string|max:255',
            'notes'         => 'nullable|string',
        ]);

        $ingredient->update($request->all());

        $redirectRoute = $request->input('redirect_to', 'dashboard.stock.index');
        return redirect()->route($redirectRoute, ['tab' => 'ingredients'])
            ->with('success', "Informasi bahan '{$ingredient->name}' berhasil diperbarui.");
    }

    /**
     * Hapus Bahan Baku
     */
    public function destroy(Request $request, Ingredient $ingredient)
    {
        $user = auth()->user();
        if ($user->isTenant() && $user->tenant && $ingredient->tenant_id !== $user->tenant->id) {
            abort(403, 'Unauthorized action.');
        }

        $ingredient->delete();

        $redirectRoute = $request->input('redirect_to', 'dashboard.stock.index');
        return redirect()->route($redirectRoute, ['tab' => 'ingredients'])
            ->with('success', "Bahan '{$ingredient->name}' berhasil dihapus dari inventaris.");
    }
}
