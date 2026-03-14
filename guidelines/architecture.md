<!--
Feature: Agent Guidelines / Architecture
Purpose: Define the architecture standard agents must follow when creating or changing application code.
Dependencies: plans/docker-setup/ai_engineering_guidelines_updated.txt
-->
# Architecture Guideline

This project follows a domain-first, feature-based architecture that keeps business logic grouped by feature and keeps technical concerns separate from domain concerns.

## Core Principles

- Organize code by business feature, not by technical layer.
- Follow Domain Driven Design, Clean Architecture boundaries, and SOLID principles.
- Keep responsibilities small and explicit so agents can reason about code safely.
- Prefer dependency injection over static helpers.

## Target Application Structure

```text
app/
  Domain/
    <Feature>/
      Actions/
      DTOs/
      Models/
      Rules/
      Services/
  Http/
    Controllers/
    Requests/
    Resources/
  Infrastructure/
    <TechnicalConcern>/
```

All new business features should be created inside `app/Domain/<Feature>/`.

## Layer Responsibilities

### Domain

- `Actions`: one use case per class, orchestrating the workflow for the feature.
- `DTOs`: explicit input and output data contracts.
- `Models`: business entities and their behavior.
- `Rules`: business constraints and policies.
- `Services`: reusable business logic and calculations that do not belong to a single model.

Actions live inside the feature module so the use case stays close to the domain it belongs to, even though the action behaves like application-layer orchestration.

### Http

- Controllers receive requests, delegate to actions, and return responses.
- Requests handle validation and authorization.
- Resources transform application data into API responses.

Controllers must stay thin. Business logic does not belong in controllers.

### Infrastructure

- Handles framework and technical integrations such as persistence, queues, external APIs, caching, and file storage.
- May depend on Laravel or external SDKs.
- Must not pull framework concerns into the domain layer.

## Structural Rules

- Never create global service folders that mix unrelated features.
- Keep cross-domain coupling explicit and minimal.
- Name classes consistently, for example `CreateSaleAction`, `CreateSaleData`, `SaleCalculatorService`, `SaleRepository`, `SaleResource`, and `StoreSaleRequest`.
- Prefer adding a new action or service over growing a god class.

## SOLID Expectations

- Single Responsibility: each class should have one reason to change.
- Open/Closed: extend behavior with new classes or collaborators instead of branching through unrelated code.
- Liskov Substitution: abstractions and implementations must remain behaviorally compatible.
- Interface Segregation: keep contracts narrow and task-focused.
- Dependency Inversion: depend on abstractions and inject collaborators where reasonable.

## Current Repo Guidance

- The current repo still uses default Laravel locations such as `app/Models` and route closures.
- New business capability should move toward the target `app/Domain/<Feature>/...` structure instead of expanding framework-default shortcuts.
- When touching an existing skeleton endpoint, use the relevant feature guideline to decide whether the change stays a small bootstrap concern or becomes a real domain feature that needs domain structure.
