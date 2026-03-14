<!--
Feature: Agent Guidelines / Entry Point
Purpose: Define the mandatory first-read workflow for agents and route each task to the correct guideline files.
Dependencies: guidelines/architecture.md, guidelines/testing.md, guidelines/file-documentation.md, guidelines/features/operational-health.md, guidelines/features/api-foundation.md, guidelines/features/authentication.md
-->
# Guidelines Entry Point

This file is the mandatory entry point for every agent task in this repository.

## Required Workflow

1. Read this file first.
2. Identify the directly affected feature or features.
3. Open only the linked feature guideline for each affected feature.
4. Open only the shared guideline files explicitly referenced by those feature guidelines.
5. Before completing the task, update every affected feature guideline if behavior, structure, dependencies, or tests changed.

Do not start implementation from repo-wide guesswork when a feature guideline already exists.

## Shared Guidelines

- [Architecture](./architecture.md): domain-driven structure, SOLID, thin controllers, actions as use cases, and domain versus infrastructure boundaries.
- [Testing](./testing.md): mandatory TDD workflow, unit/integration/e2e coverage, and regression test requirements for bug fixes.
- [File Documentation](./file-documentation.md): required inline file ownership and dependency documentation for newly created files.

## Feature Selection Rules

- Single-feature task: read this file, then open the matching feature guideline.
- Multi-feature task: read this file, then open every directly affected feature guideline.
- Shared infrastructure task: read this file, then open the relevant feature guideline plus any shared guideline that governs the change.
- New feature task: create a new file at `guidelines/features/<feature-name>.md` before the implementation is considered complete.

## Feature Index

| Feature | Guideline | Current Repo Touchpoints |
| --- | --- | --- |
| Operational Health | [guidelines/features/operational-health.md](./features/operational-health.md) | `routes/web.php`, `tests/Feature/HealthCheckTest.php` |
| API Foundation | [guidelines/features/api-foundation.md](./features/api-foundation.md) | `routes/api.php`, `tests/Feature/ApiPingTest.php` |
| Authentication | [guidelines/features/authentication.md](./features/authentication.md) | `routes/api.php`, `laravel/sanctum` |

If a task touches code that does not fit any feature above, create a new feature guideline and add it to this index as part of the same change.

## Update Rules

- Every feature change must update its corresponding feature guideline if the change affects purpose, flows, structure, dependencies, or tests.
- Every new file must follow the inline documentation standard in [File Documentation](./file-documentation.md).
- Every bug fix must add a regression test under the appropriate test layer.
- Every implementation must follow TDD and SOLID as defined in the shared guidelines.

## Current Architecture Note

The repo is still near Laravel skeleton state. The target architecture for business features is `app/Domain/<Feature>/...` even when the current implementation still lives in framework-default locations.
