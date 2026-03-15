<?php

/**
 * Feature: Authentication
 * Purpose: Return the currently authenticated API user from the shared auth domain.
 * Dependencies: App\Http\Resources\Authentication\UserResource, Illuminate\Http\Request
 */

namespace App\Http\Controllers\Api\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Resources\Authentication\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetAuthenticatedUserController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json([
            'user' => UserResource::make($request->user()),
        ]);
    }
}
