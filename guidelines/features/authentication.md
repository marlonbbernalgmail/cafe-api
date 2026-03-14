<!--
Feature: Authentication
Purpose: Document the repository feature responsible for authenticated API access and Sanctum-based identity boundaries.
Dependencies: ../README.md, ../architecture.md, ../testing.md, ../../routes/api.php, ../../composer.json
-->
# Authentication

Back to the entry point: [guidelines/README.md](../README.md)

## Feature Purpose

Authentication covers access to API behavior that requires a verified user context and any surrounding identity or authorization wiring needed to protect endpoints.

## Owned Domain Area

- Authenticated API access
- User identity boundaries
- Middleware-protected route access

If authentication logic grows beyond route-level framework wiring, introduce domain-oriented actions and services under `app/Domain/Authentication/`.

## Current Implementation Touchpoints

- `routes/api.php`
- `/api/user`
- `auth:sanctum` middleware
- `composer.json` dependency on `laravel/sanctum`

## Main Flows and Use Cases

- Return the authenticated user for protected API requests.
- Reject unauthenticated access to protected endpoints.
- Provide the foundation for future login, token, and session-related flows.

## Related Models, Actions, and Services

Current state:

- `app/Models/User.php`
- No dedicated authentication actions or services yet.

Future direction:

- Add explicit authentication actions, DTOs, and services when auth behavior becomes more than default framework wiring.

## Dependencies

- Laravel Sanctum
- Laravel authentication middleware
- User model and future auth token or session infrastructure

## Testing Expectations

- Integration tests must verify both authenticated success and unauthenticated failure cases.
- End-to-end tests should cover any complete sign-in or token flow once implemented.
- Unit tests are required for extracted authentication rules, token policies, or supporting services.
- Bug fixes in auth must always include regression tests because auth failures are high-impact.

## Update Requirement

Update this guideline whenever protected routes, authentication flow, middleware behavior, identity contracts, or auth dependencies change.

## Shared Guidelines To Read With This File

- [Architecture](../architecture.md)
- [Testing](../testing.md)
- [File Documentation](../file-documentation.md)
