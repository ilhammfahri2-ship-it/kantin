<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        
        $query = Product::latest();

        // Jika user adalah tenant, filter produk berdasarkan tenant miliknya
        if ($user && $user->isTenant() && $user->tenant) {
            $query->where('tenant_id', $user->tenant->id);
        }

        $products = $query->paginate(10);
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->except('image');
        $data['slug'] = Str::slug($request->name . '-' . time());
        
        // Tetapkan tenant_id secara otomatis untuk user tenant
        if (auth()->user()->isTenant() && auth()->user()->tenant) {
            $data['tenant_id'] = auth()->user()->tenant->id;
        }

        $data['is_available'] = $request->has('is_available') ? true : false;
        $data['is_featured'] = $request->has('is_featured') ? true : false;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = $path;
        }

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        // Pastikan tenant hanya bisa edit produknya sendiri
        $user = auth()->user();
        if ($user->isTenant() && $user->tenant && $product->tenant_id !== $user->tenant->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        // Pastikan tenant hanya bisa edit produknya sendiri
        $user = auth()->user();
        if ($user->isTenant() && $user->tenant && $product->tenant_id !== $user->tenant->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->except('image');
        
        if ($request->name !== $product->name) {
            $data['slug'] = Str::slug($request->name . '-' . time());
        }

        $data['is_available'] = $request->has('is_available') ? true : false;
        $data['is_featured'] = $request->has('is_featured') ? true : false;

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada dan bukan default/URL luar
            if ($product->image && !str_starts_with($product->image, 'http') && !str_starts_with($product->image, 'images/')) {
                Storage::disk('public')->delete($product->image);
            }
            
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = $path;
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Pastikan tenant hanya bisa hapus produknya sendiri
        $user = auth()->user();
        if ($user->isTenant() && $user->tenant && $product->tenant_id !== $user->tenant->id) {
            abort(403, 'Unauthorized action.');
        }

        if ($product->image && !str_starts_with($product->image, 'http') && !str_starts_with($product->image, 'images/')) {
            Storage::disk('public')->delete($product->image);
        }
        
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
