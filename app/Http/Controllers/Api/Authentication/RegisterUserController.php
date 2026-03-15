<?php

/**
 * Feature: Authentication
 * Purpose: Accept API registration requests and delegate shared-auth user creation to the domain layer.
 * Dependencies: App\Domain\Authentication\Actions\RegisterUserAction, App\Http\Requests\Authentication\RegisterUserRequest, App\Http\Resources\Authentication\UserResource
 */

namespace App\Http\Controllers\Api\Authentication;

use App\Domain\Authentication\Actions\RegisterUserAction;
use App\Domain\Authentication\DTOs\RegisterUserData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\RegisterUserRequest;
use App\Http\Resources\Authentication\UserResource;
use Illuminate\Http\JsonResponse;

class RegisterUserController extends Controller
{
    public function __invoke(RegisterUserRequest $request, RegisterUserAction $registerUser): JsonResponse
    {
        $session = $registerUser(RegisterUserData::fromRequest($request));

        return response()->json([
            'token_type' => $session->tokenType,
            'access_token' => $session->accessToken,
            'user' => UserResource::make($session->user),
        ], 201);
    }
}
