<!--
Feature: Operational Health
Purpose: Document the repository feature responsible for service availability checks and basic status endpoints.
Dependencies: ../README.md, ../architecture.md, ../testing.md, ../../routes/web.php, ../../tests/Feature/HealthCheckTest.php
-->
# Operational Health

Back to the entry point: [guidelines/README.md](../README.md)

## Feature Purpose

Operational Health covers basic service availability signals used by developers, containers, and hosting platforms to confirm the application is alive.

## Owned Domain Area

- Platform operations
- Service health and readiness signaling
- Lightweight status responses for runtime verification

If this area grows beyond bootstrap routes, move the behavior into `app/Domain/OperationalHealth/`.

## Current Implementation Touchpoints

- `routes/web.php`
- `/up` health check endpoint provided by Laravel
- root `/` status JSON used as a simple service landing response
- `tests/Feature/HealthCheckTest.php`

## Main Flows and Use Cases

- Return a successful response for `/up`.
- Return a lightweight JSON response for `/` that indicates the service is running.
- Support runtime health checks from Docker, AWS, or other infrastructure.

## Related Models, Actions, and Services

Current state:

- No dedicated domain models, actions, or services yet.

Future direction:

- Add `app/Domain/OperationalHealth/Actions/...` if health behavior becomes more than a simple platform concern.

## Dependencies

- Laravel routing
- Laravel health endpoint behavior
- Deployment infrastructure that uses health checks

## Testing Expectations

- Integration tests must verify `/up` and any public status endpoint behavior.
- End-to-end tests should cover the health flow when deployment or container behavior depends on it.
- Unit tests are only needed if health logic is extracted into rules or services.

## Update Requirement

Update this guideline whenever health endpoints, payloads, monitoring behavior, or deployment expectations change.

## Shared Guidelines To Read With This File

- [Architecture](../architecture.md)
- [Testing](../testing.md)
- [File Documentation](../file-documentation.md)
