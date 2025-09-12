<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use App\Jobs\ReplicateToSlave;

class UserController extends Controller
{
    /**
     * GET /api/v1/users
     * List all users (read from slave)
     */
    public function index()
    {
        Config::set('database.default', 'slave');

        $users = User::with(['profile', 'orders'])->get();

        return response()->json($users, Response::HTTP_OK);
    }

    /**
     * POST /api/v1/users
     * Create a new user (write to master)
     */
    public function store(Request $request)
    {
        Config::set('database.default', 'mysql');

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);

        $user = User::create($validated);

        // Dispatch job using helper function
        dispatch(new ReplicateToSlave($user));

        return response()->json($user, Response::HTTP_CREATED);
    }

    /**
     * GET /api/v1/users/{id}
     * Show a single user (read from slave)
     */
    public function show($id)
    {
        Config::set('database.default', 'slave');

        $user = User::with(['profile', 'orders'])->find($id);

        if (! $user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($user, Response::HTTP_OK);
    }
}
