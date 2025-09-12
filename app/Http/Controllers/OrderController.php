<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;

class OrderController extends Controller
{
    /**
     * GET /api/v1/orders
     * List all orders (read from slave)
     */
    public function index()
    {
        Config::set('database.default', 'slave');

        $orders = Order::with(['user', 'products'])->get();

        return response()->json($orders, Response::HTTP_OK);
    }

    /**
     * POST /api/v1/orders
     * Create a new order (write to master)
     */
    public function store(Request $request)
    {
        Config::set('database.default', 'mysql');

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        $order = Order::create([
            'user_id' => $validated['user_id'],
        ]);

        // Attach products to the order
        $order->products()->attach($validated['product_ids']);

        return response()->json($order->load('products'), Response::HTTP_CREATED);
    }

    /**
     * GET /api/v1/orders/{id}
     * Show a single order (read from slave)
     */
    public function show($id)
    {
        Config::set('database.default', 'slave');

        $order = Order::with(['user', 'products'])->find($id);

        if (! $order) {
            return response()->json(['message' => 'Order not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($order, Response::HTTP_OK);
    }
}
