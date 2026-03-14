<!--
Feature: Agent Guidelines / File Documentation
Purpose: Define the required file-level ownership and dependency documentation standard for every newly created file.
Dependencies: guidelines/README.md, guidelines/features
-->
# File Documentation Guideline

Every newly created source or documentation file must explain what feature it belongs to and what it depends on so an agent can understand the file immediately on open.

## Required Fields

Every new file must document:

- `Feature`: the owning feature or cross-cutting area.
- `Purpose`: the reason the file exists.
- `Dependencies`: the direct collaborators, frameworks, services, or related guideline files the file relies on.
- `Notes` when useful: related tests, constraints, or special behavior.

## Required Placement

- Put the documentation at the top of the file.
- Use the native comment style for that file type whenever possible.
- Keep the header concise and accurate.

## Preferred Header Examples

### PHP

```php
<?php

/**
 * Feature: Authentication
 * Purpose: Return the authenticated API user.
 * Dependencies: Illuminate\Http\Request, auth:sanctum middleware
 * Notes: Covered by tests/Feature/Integration/Authentication/GetAuthenticatedUserTest.php
 */
```

### JavaScript or TypeScript

```ts
/**
 * Feature: Sales
 * Purpose: Submit a sale creation request from the admin client.
 * Dependencies: apiClient, CreateSaleData
 */
```

### Markdown

```md
<!--
Feature: Sales
Purpose: Document the Sales feature for task-scoped agent context.
Dependencies: guidelines/README.md, guidelines/architecture.md, guidelines/testing.md
-->
```

## Commentless File Types

If a committed file format does not support inline comments, use one of these approaches:

- Prefer a nearby wrapper file that supports comments if the format allows that design.
- If the format truly cannot contain inline documentation, create a sibling file named `<filename>.meta.md` with the same required fields and link it from the owning feature guideline.

This fallback should be rare. Prefer file types and patterns that support inline ownership documentation.

## Quality Rules

- Keep dependencies direct; do not list the entire framework.
- Update the header when the file changes ownership, purpose, or collaborators.
- Generated files should document the generating source rather than hand-maintained internals.
