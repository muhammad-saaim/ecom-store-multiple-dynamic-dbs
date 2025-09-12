<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use App\Jobs\ReplicateToSlave;

class ProfileController extends Controller
{
    /**
     * GET /api/v1/users/{user}/profile
     * Read profile (read from slave)
     */
    public function show($userId)
    {
        Config::set('database.default', 'slave');

        $user = User::with('profile')->find($userId);

        if (! $user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($user->profile, Response::HTTP_OK);
    }

    /**
     * POST /api/v1/users/{user}/profile
     * Create or update profile for a user (write to master)
     */
    public function store(Request $request, $userId)
    {
        Config::set('database.default', 'mysql');

        $user = User::find($userId);
        if (! $user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'bio' => 'nullable|string|max:2000',
        ]);

        $profile = $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            ['bio' => $validated['bio'] ?? null]
        );

        // Dispatch job using helper function
        dispatch(new ReplicateToSlave($profile));

        return response()->json($profile, Response::HTTP_CREATED);
    }

    /**
     * PUT /api/v1/users/{user}/profile
     * Update profile (write to master)
     */
    public function update(Request $request, $userId)
    {
        Config::set('database.default', 'mysql');

        $user = User::with('profile')->find($userId);
        if (! $user || ! $user->profile) {
            return response()->json(['message' => 'User or profile not found'], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'bio' => 'nullable|string|max:2000',
        ]);

        $user->profile->update($validated);

        // Dispatch job using helper function
        dispatch(new ReplicateToSlave($user->profile));

        return response()->json($user->profile, Response::HTTP_OK);
    }

    /**
     * DELETE /api/v1/users/{user}/profile
     * Delete the profile (write to master)
     */
    public function destroy($userId)
    {
        Config::set('database.default', 'mysql');

        $user = User::with('profile')->find($userId);
        if (! $user || ! $user->profile) {
            return response()->json(['message' => 'User or profile not found'], Response::HTTP_NOT_FOUND);
        }

        $profile = $user->profile;
        $profile->delete();

        // Dispatch job using helper function to delete from slave
        dispatch(new ReplicateToSlave($profile));

        return response()->json(['message' => 'Profile deleted'], Response::HTTP_OK);
    }
}
