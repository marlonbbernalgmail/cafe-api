<?php

/**
 * Feature: Authentication
 * Purpose: Return the resolved presence status for a shared-auth user.
 * Dependencies: App\Domain\Authentication\Actions\GetUserPresenceAction, App\Models\User
 */

namespace App\Http\Controllers\Api\Authentication;

use App\Domain\Authentication\Actions\GetUserPresenceAction;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class GetUserPresenceController extends Controller
{
    public function __invoke(User $user, GetUserPresenceAction $getUserPresence): JsonResponse
    {
        return response()->json([
            'presence' => $getUserPresence($user)->toArray(),
        ]);
    }
}
