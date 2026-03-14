<!--
Feature: Agent Guidelines / Testing
Purpose: Define the required TDD workflow and the minimum testing expectations for all changes.
Dependencies: phpunit.xml, tests/TestCase.php, tests/Unit, tests/Feature
-->
# Testing Guideline

Testing is mandatory for every task. This repository follows a TDD-first workflow.

## Required Workflow

1. Write or update the failing test first.
2. Implement the smallest change needed to make the test pass.
3. Refactor while keeping the full test suite green.
4. Update feature documentation when behavior, dependencies, or test coverage changed.

Do not treat tests as a follow-up cleanup step.

## Required Test Layers

Every change should include the test layers that apply to the behavior being modified.

- Unit tests: isolated logic, rules, services, DTO transformations, and small domain behavior.
- Integration tests: framework wiring, routes, middleware, persistence, queues, events, and interactions between application layers.
- End-to-end tests: complete user or system flows that prove the full path works from entry point to result.

## Laravel Test Mapping

Current repo layout:

- `tests/Unit`: unit tests.
- `tests/Feature`: application and HTTP-level tests.

Preferred structure going forward:

- `tests/Unit/<Feature>/...`
- `tests/Feature/Integration/<Feature>/...`
- `tests/Feature/E2E/<Feature>/...`

If the repo does not yet contain the target subfolders, create them when a task needs them.

## Bug Fix Rule

Every bug fix must include a regression test.

Expected bug-fix order:

1. Reproduce the bug with a failing regression test.
2. Apply the fix.
3. Keep the regression test in the suite so the bug cannot silently return.

## Coverage Expectations

- New domain logic should usually have unit tests and at least one integration test for wiring.
- New API endpoints should usually have integration tests and, when part of a critical workflow, an end-to-end test.
- Infrastructure-heavy changes should verify both integration behavior and failure handling.
- If a test layer does not apply, the change description and updated feature guideline should make that explicit.

## Minimum Completion Standard

A task is not complete unless:

- the relevant failing test existed first,
- the final implementation passes the applicable unit, integration, and end-to-end coverage expectations,
- any bug fix has a regression test,
- and the related feature guideline reflects the updated behavior.
