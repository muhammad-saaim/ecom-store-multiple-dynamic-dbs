<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        $count = DB::connection('analytics')->table('monthly_users')->sum('count');

        return response()->json(['total_users' => $count], Response::HTTP_OK);
    }

    /**
     * GET /api/v1/analytics/users/monthly
     * New users per month
     */
    public function newUsersMonthly()
    {
        $data = DB::connection('analytics')
            ->table('monthly_users')
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
        $count = DB::connection('analytics')->table('revenue_per_month')->sum('orders_count');

        return response()->json(['total_orders' => $count], Response::HTTP_OK);
    }

    /**
     * GET /api/v1/analytics/orders/revenue
     * Revenue per month
     */
    public function revenuePerMonth()
    {
        $data = DB::connection('analytics')
            ->table('revenue_per_month')
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
        $data = DB::connection('analytics')
            ->table('top_products')
            ->orderByDesc('sold')
            ->limit(10)
            ->get();

        return response()->json($data, Response::HTTP_OK);
    }

    /**
     * GET /api/v1/analytics/page-views
     * Page views from analytics DB
     */
    public function pageViews()
    {
        $data = DB::connection('analytics')
            ->table('page_views')
            ->orderByDesc('count')
            ->get();

        return response()->json($data, Response::HTTP_OK);
    }
}
