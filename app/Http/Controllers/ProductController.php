<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;

class ProductController extends Controller
{
    /**
     * GET /api/v1/products
     * List all products (read from slave)
     */
    public function index()
    {
        Config::set('database.default', 'slave');

        $products = Product::with('orders')->get();

        return response()->json($products, Response::HTTP_OK);
    }

    /**
     * POST /api/v1/products
     * Create a new product (write to master)
     */
    public function store(Request $request)
    {
        Config::set('database.default', 'mysql');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
        ]);

        $product = Product::create($validated);

        return response()->json($product, Response::HTTP_CREATED);
    }

    /**
     * GET /api/v1/products/{id}
     * Show a single product (read from slave)
     */
    public function show($id)
    {
        Config::set('database.default', 'slave');

        $product = Product::with('orders')->find($id);

        if (! $product) {
            return response()->json(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($product, Response::HTTP_OK);
    }
}
