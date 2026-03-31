<?php

/**
 * Feature: Authentication
 * Purpose: Accept authenticated mobile heartbeat calls so the backend can track which users are currently online.
 * Dependencies: App\Domain\Authentication\Actions\RecordAuthenticatedUserPresenceHeartbeatAction, Illuminate\Http\Request
 */

namespace App\Http\Controllers\Api\Authentication;

use App\Domain\Authentication\Actions\RecordAuthenticatedUserPresenceHeartbeatAction;
use App\Domain\Authentication\DTOs\UserPresenceHeartbeatData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\RecordUserPresenceHeartbeatRequest;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;

class RecordAuthenticatedUserPresenceHeartbeatController extends Controller
{
    public function __invoke(
        RecordUserPresenceHeartbeatRequest $request,
        RecordAuthenticatedUserPresenceHeartbeatAction $recordHeartbeat,
    ): JsonResponse {
        $user = $request->user();
        $token = $user?->currentAccessToken();

        if (! $user instanceof User || $token === null) {
            throw new AuthenticationException('Presence heartbeat requires a valid personal access token.');
        }

        return response()->json([
            'presence' => $recordHeartbeat(
                $user,
                $token,
                UserPresenceHeartbeatData::fromRequest($request),
            )->toArray(),
        ]);
    }
}
