<?php

/**
 * Feature: Authentication
 * Purpose: Accept API login requests and delegate credential verification to the domain layer.
 * Dependencies: App\Domain\Authentication\Actions\LoginUserAction, App\Http\Requests\Authentication\LoginUserRequest, App\Http\Resources\Authentication\UserResource
 */

namespace App\Http\Controllers\Api\Authentication;

use App\Domain\Authentication\Actions\LoginUserAction;
use App\Domain\Authentication\DTOs\LoginUserData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\LoginUserRequest;
use App\Http\Resources\Authentication\UserResource;
use Illuminate\Http\JsonResponse;

class LoginUserController extends Controller
{
    public function __invoke(LoginUserRequest $request, LoginUserAction $loginUser): JsonResponse
    {
        $session = $loginUser(LoginUserData::fromRequest($request));

        return response()->json([
            'token_type' => $session->tokenType,
            'access_token' => $session->accessToken,
            'user' => UserResource::make($session->user),
        ]);
    }
}
