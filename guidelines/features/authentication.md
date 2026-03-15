<!--
Feature: Authentication
Purpose: Document the repository feature responsible for authenticated API access and Sanctum-based identity boundaries.
Dependencies: ../README.md, ../architecture.md, ../testing.md, ../../routes/api.php, ../../composer.json
-->
# Authentication

Back to the entry point: [guidelines/README.md](../README.md)

## Feature Purpose

Authentication covers the portable Users Auth implementation reused across POS APIs. It owns shared user registration, login, logout, current-user retrieval, Sanctum token issuance, and the shared users-database boundary that keeps auth separate from each API's business database.

## Owned Domain Area

- Shared user registration and login
- API token issuance and revocation
- Authenticated API access and current-user retrieval
- Shared users database connection boundary
- Middleware-protected route access

Authentication logic belongs under `app/Domain/Authentication/` and supporting HTTP or infrastructure layers instead of route closures.

## Current Implementation Touchpoints

- `routes/api.php`
- `/api/auth/register`
- `/api/auth/login`
- `/api/auth/me`
- `/api/auth/logout`
- `/api/user`
- `app/Domain/Authentication/`
- `app/Http/Controllers/Api/Authentication/`
- `app/Http/Requests/Authentication/`
- `app/Http/Resources/Authentication/`
- `app/Infrastructure/Authentication/`
- `app/Models/User.php`
- `config/authentication.php`
- `config/database.php`
- `database/migrations/0001_01_01_000000_create_users_table.php`
- `database/migrations/2026_03_14_210346_create_personal_access_tokens_table.php`
- `composer.json` dependency on `laravel/sanctum`
- `tests/Feature/Integration/Authentication/`
- `tests/Feature/E2E/Authentication/`
- `tests/Unit/Authentication/`

## Main Flows and Use Cases

- Register a shared-auth user and issue a Sanctum access token.
- Verify shared-auth user credentials and issue a Sanctum access token.
- Return the authenticated user for protected token-authenticated requests.
- Revoke the current access token on logout.
- Reject unauthenticated access to protected endpoints.
- Preserve `/api/user` as a protected alias for current-user retrieval.

## Related Models, Actions, and Services

Current state:

- `app/Models/User.php`
- `app/Infrastructure/Authentication/Models/PersonalAccessToken.php`
- `app/Domain/Authentication/Actions/RegisterUserAction.php`
- `app/Domain/Authentication/Actions/LoginUserAction.php`
- `app/Domain/Authentication/Actions/LogoutCurrentUserAction.php`
- `app/Domain/Authentication/DTOs/RegisterUserData.php`
- `app/Domain/Authentication/DTOs/LoginUserData.php`
- `app/Domain/Authentication/DTOs/IssuedApiTokenData.php`
- `app/Domain/Authentication/Services/IssueUserApiTokenService.php`

## Portable Reuse Rules

- Every POS API should reuse the same Users Auth implementation or extracted internal package instead of re-creating auth logic per API.
- Each POS API may keep its own business database, but user identity and auth-token data belong to the shared users database.
- Configure the shared users database through `AUTH_USERS_DB_CONNECTION` and the `users` connection in `config/database.php`.
- If the shared users connection is not configured, auth models fall back to the application's default database connection.
- This repo currently implements token-based API auth for portable reuse. Web-specific SPA cookie auth can be layered later without splitting the underlying user identity domain.

## Dependencies

- Laravel Sanctum
- Laravel authentication middleware
- Shared users database configuration
- User model and Sanctum token infrastructure

## Testing Expectations

- Integration tests must verify register, login, current-user, logout, and unauthenticated failure behavior.
- End-to-end tests should cover the complete register or login through logout token flow.
- Unit tests are required for extracted authentication rules, token policies, or supporting services such as connection resolution.
- Bug fixes in auth must always include regression tests because auth failures are high-impact.

## Update Requirement

Update this guideline whenever auth endpoints, authentication flow, middleware behavior, shared users connection behavior, identity contracts, or auth dependencies change.

## Shared Guidelines To Read With This File

- [Architecture](../architecture.md)
- [Testing](../testing.md)
- [File Documentation](../file-documentation.md)
