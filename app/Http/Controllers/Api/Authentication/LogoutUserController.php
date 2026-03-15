<?php

/**
 * Feature: Authentication
 * Purpose: Revoke the current authenticated user's active API token.
 * Dependencies: App\Domain\Authentication\Actions\LogoutCurrentUserAction, Illuminate\Http\Request
 */

namespace App\Http\Controllers\Api\Authentication;

use App\Domain\Authentication\Actions\LogoutCurrentUserAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LogoutUserController extends Controller
{
    public function __invoke(Request $request, LogoutCurrentUserAction $logoutCurrentUser): Response
    {
        $logoutCurrentUser($request->user());

        return response()->noContent();
    }
}
