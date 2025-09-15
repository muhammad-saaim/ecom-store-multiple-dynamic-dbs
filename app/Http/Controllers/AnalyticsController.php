<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;

class AnalyticsController extends Controller
{
    /**
     * GET /api/v1/analytics/users/count
     * Total number of users
     */
    public function totalUsers()
    {
        Config::set('database.default', 'slave'); // read from slave

        $count = User::count();

        return response()->json(['total_users' => $count], Response::HTTP_OK);
    }

    /**
     * GET /api/v1/analytics/users/monthly
     * New users per month
     */
    public function newUsersMonthly()
    {
        Config::set('database.default', 'slave');

        $data = User::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return response()->json($data, Response::HTTP_OK);
    }

    /**
     * GET /api/v1/analytics/orders/count
     * Total number of orders
     */
    public function totalOrders()
    {
        Config::set('database.default', 'slave');

        $count = Order::count();

        return response()->json(['total_orders' => $count], Response::HTTP_OK);
    }

    /**
     * GET /api/v1/analytics/orders/revenue
     * Revenue per month
     */
    public function revenuePerMonth()
    {
        Config::set('database.default', 'slave');

        $data = Order::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(total) as revenue')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return response()->json($data, Response::HTTP_OK);
    }

    /**
     * GET /api/v1/analytics/products/top
     * Top-selling products
     */
    public function topProducts()
    {
        Config::set('database.default', 'slave');

        $data = DB::table('order_product')
            ->join('products', 'order_product.product_id', '=', 'products.id')
            ->select('products.id', 'products.name', DB::raw('COUNT(order_product.product_id) as total_sales'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sales')
            ->limit(10)
            ->get();

        return response()->json($data, Response::HTTP_OK);
    }

    /**
     * OPTIONAL: GET /api/v1/analytics/page-views
     * Read page views from analytics DB
     */
    public function pageViews()
    {
        $data = DB::connection('analytics')
            ->table('page_views')
            ->select('page', 'count')
            ->orderByDesc('count')
            ->get();

        return response()->json($data, Response::HTTP_OK);
    }
}
