<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        // Mengambil data produk dari database MySQL
        $products = $this->productService->getProducts();

        return response()->json([
            'data' => $products
        ]);
    }

    // Menampilkan detail produk berdasarkan ID
    public function show($id)
    {
        $product = $this->productService->getProductById($id);

        if (!$product) {
            return response()->json([
                'message' => 'Produk tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'data' => $product
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ], [
            'name.required' => 'Nama produk harus diisi.',
            'price.required' => 'Harga produk harus diisi.',
            'price.numeric' => 'Harga produk harus berupa angka.',
            'price.min' => 'Harga produk tidak boleh kurang dari 0.',
            'stock.required' => 'Stok produk harus diisi.',
            'stock.integer' => 'Stok produk harus berupa angka bulat.',
            'stock.min' => 'Stok produk tidak boleh kurang dari 0.',
        ]);

        $product = $this->productService->createProduct($validated);

        Log::info('New Product Created in Database', $validated);

        return response()->json([
            'message' => 'Product Successfully Created',
            'data' => $product
        ], 201);
    }

    // Mengubah data produk berdasarkan ID
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
        ]);

        $product = $this->productService->updateProduct($id, $validated);

        if (!$product) {
            return response()->json([
                'message' => 'Produk tidak ditemukan untuk diperbarui.'
            ], 404);
        }

        return response()->json([
            'message' => 'Product Successfully Updated',
            'data' => $product
        ], 200);
    }

    // Menghapus data produk berdasarkan ID
    public function destroy($id)
    {
        $deleted = $this->productService->deleteProduct($id);

        if (!$deleted) {
            return response()->json([
                'message' => 'Produk tidak ditemukan untuk dihapus.'
            ], 404);
        }

        return response()->json([
            'message' => 'Product Successfully Deleted'
        ], 200);
    }
}