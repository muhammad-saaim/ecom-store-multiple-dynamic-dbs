<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class UserController extends Controller
{
    /**
     * GET /api/users
     */
    public function index()
    {
        // Example: Force read from slave DB
        Config::set('database.default', 'slave');

        return response()->json(User::all());
    }

    /**
     * POST /api/users
     */
    public function store(Request $request)
    {
        // Always write to master
        Config::set('database.default', 'mysql');

        $user = User::create($request->only(['name', 'email']));

        return response()->json($user, 201);
    }

    /**
     * GET /api/users/{id}
     */
    public function show($id)
    {
        Config::set('database.default', 'slave');

        $user = User::with(['profile', 'orders'])->findOrFail($id);

        return response()->json($user);
    }
}
