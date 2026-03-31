<?php

/**
 * Feature: Presence Heartbeat
 * Purpose: Accept a presence ping from an authenticated device and stamp the token with metadata.
 * Dependencies: App\Domain\Authentication\Actions\RecordPresenceAction, App\Domain\Authentication\DTOs\RecordPresenceData, App\Http\Requests\Authentication\RecordPresenceRequest
 */

namespace App\Http\Controllers\Api\Authentication;

use App\Domain\Authentication\Actions\RecordPresenceAction;
use App\Domain\Authentication\DTOs\RecordPresenceData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\RecordPresenceRequest;
use Illuminate\Http\Response;

class RecordPresenceController extends Controller
{
    public function __invoke(RecordPresenceRequest $request, RecordPresenceAction $recordPresence): Response
    {
        $recordPresence($request->user(), RecordPresenceData::fromRequest($request));

        return response()->noContent();
    }
}
