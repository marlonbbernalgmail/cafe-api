<!--
Feature: Deployment / AWS Runtime
Purpose: Document how to configure this API on AWS App Runner when the public URL or database endpoint changes.
Dependencies: README.md, .github/workflows/deploy.yml, compose.yaml, config/database.php, config/sanctum.php
Notes: This guide assumes Amazon ECR for images, AWS App Runner for runtime hosting, and Amazon RDS MariaDB for production data.
-->
# AWS App Runner Setup

This repository is designed to deploy the same container image to local Docker and AWS App Runner. In AWS, the values that usually change are:

- the public application URL
- the database host, port, database name, and credentials
- optional session and Sanctum host settings if you use cookie-based auth from a browser frontend

The safest pattern is to treat those values as runtime configuration and never hardcode raw server IPs into the application.

## What To Use Instead of an AWS Server IP

Do not point `APP_URL` at a raw EC2 or container IP address.

- For App Runner, use the App Runner service URL or a custom domain.
- For the database, use the RDS endpoint DNS name, not the database instance IP.

That way, infrastructure can move underneath you without forcing code changes.

Examples:

```env
APP_URL=https://abc123.ap-southeast-1.awsapprunner.com
DB_HOST=my-db.abcdefghijkl.ap-southeast-1.rds.amazonaws.com
```

Better for production:

```env
APP_URL=https://api.example.com
DB_HOST=prod-cafe-db.abcdefghijkl.ap-southeast-1.rds.amazonaws.com
```

## One-Time AWS Setup

### 1. Create the ECR repository

Create an Amazon ECR repository for this app. The GitHub Actions workflow pushes the image tags `main` and `<commit-sha>` to that repository.

Set this GitHub repository variable:

- `AWS_REGION`
- `ECR_REPOSITORY`

### 2. Configure GitHub to deploy to AWS

This repository already includes [deploy.yml](/c:/apis/cafe-api/.github/workflows/deploy.yml), which:

1. runs tests
2. builds the Docker image
3. runs `php artisan migrate --force`
4. pushes the image to ECR
5. expects App Runner to auto-deploy from the `main` tag

Set this GitHub repository secret:

- `AWS_ROLE_TO_ASSUME`

The IAM role behind that secret should have permission to push to ECR and read whatever deployment secrets you keep in GitHub or AWS.

### 3. Create the production database

Create an Amazon RDS MariaDB instance.

Use these values from RDS:

- endpoint DNS name -> `DB_HOST`
- port -> `DB_PORT`
- database name -> `DB_DATABASE`
- master or app user -> `DB_USERNAME`
- password -> `DB_PASSWORD`

If you keep all app data in one database, that is enough.

If you split user data into a separate database, this application supports separate `USERS_DB_*` values. If those are not set, the `users` connection falls back to the main `DB_*` settings from [config/database.php](/c:/apis/cafe-api/config/database.php):85.

### 4. Create the App Runner service

Create an App Runner service from the ECR repository and configure it to auto-deploy when the `main` image tag changes.

In the App Runner service runtime configuration, set:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_KEY=<production app key>`
- `APP_URL=<public https URL>`
- `LOG_CHANNEL=stderr`
- `DB_CONNECTION=mariadb`
- `DB_HOST=<rds endpoint>`
- `DB_PORT=3306`
- `DB_DATABASE=<database name>`
- `DB_USERNAME=<database username>`
- `DB_PASSWORD=<database password>`
- `OCTANE_SERVER=frankenphp`

You do not need to set `PORT` to `8001` in App Runner. App Runner injects its own `PORT`, and this container already reads that value at startup.

## Required GitHub Variables And Secrets

The deploy workflow currently uses these GitHub values.

### Repository variables

- `AWS_REGION`
- `ECR_REPOSITORY`
- `APP_URL`

### Repository secrets

- `AWS_ROLE_TO_ASSUME`
- `APP_KEY`
- `DB_HOST`
- `DB_PORT`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`

Generate `APP_KEY` once and keep it stable across deployments. A simple way to create one is:

```sh
php artisan key:generate --show
```

Important: `APP_URL` and the database secrets in GitHub are used by the migration step in the workflow. The runtime values in App Runner are used by the live app after deployment. Keep both places in sync.

## Recommended Production Networking

### Recommended

Use:

- App Runner for the public API
- a VPC connector from App Runner into your VPC
- a private RDS MariaDB instance

This avoids depending on public database access rules and keeps the database off the public internet.

### Quick-start but less ideal

Use:

- App Runner with the default public networking
- a publicly reachable RDS instance

This is simpler to bootstrap, but it is not the best long-term production setup.

## Important Migration Caveat

The current GitHub Actions workflow runs migrations from a GitHub-hosted runner before it pushes the new image.

That means:

- if your RDS instance is publicly reachable and your network rules allow it, the current migration step can work
- if your RDS instance is private inside a VPC, the current migration step will not reach it from GitHub-hosted runners

If you use private RDS, choose one of these approaches:

1. move migrations into AWS, such as a one-off task or an administrative container run inside the VPC
2. use a self-hosted GitHub Actions runner inside the VPC
3. temporarily keep database access public during early setup, then harden it later

For a production system, option 1 or 2 is the better direction.

Additional repo-specific note: the current [deploy.yml](/c:/apis/cafe-api/.github/workflows/deploy.yml) migration step passes only the primary `DB_*` values into the migration container. If your production setup truly requires a separate `USERS_DB_*` connection during migrations, extend that workflow before relying on split-database migrations in CI.

## How To Handle A Public URL Change

If the App Runner URL changes or you switch to a custom domain:

1. Update App Runner runtime environment variable `APP_URL`.
2. Update GitHub repository variable `APP_URL`.
3. If a browser SPA uses Sanctum cookies, update:
   - `SESSION_DOMAIN`
   - `SANCTUM_STATEFUL_DOMAINS`
4. Redeploy the App Runner service.
5. Verify the following endpoints:
   - `GET /`
   - `GET /up`
   - `GET /api/ping`

Notes:

- Prefer a custom domain such as `https://api.example.com` so `APP_URL` stays stable even if the underlying App Runner service is recreated.
- If you only use bearer tokens and not cookie-based SPA auth, `SESSION_DOMAIN` and `SANCTUM_STATEFUL_DOMAINS` may be unnecessary.

## How To Handle A Database Change

If the database endpoint, name, or credentials change:

1. Update the App Runner runtime values for `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD`.
2. Update the matching GitHub repository secrets so the deploy workflow can still run migrations.
3. If you use a separate users database, update `USERS_DB_HOST`, `USERS_DB_PORT`, `USERS_DB_DATABASE`, `USERS_DB_USERNAME`, and `USERS_DB_PASSWORD` in App Runner too.
4. Redeploy the service.
5. Run or verify migrations against the correct database.

Use the RDS endpoint DNS name, not a raw IP address. AWS can move the underlying infrastructure while keeping the endpoint name stable.

## Suggested Runtime Configuration Template

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:replace-me
APP_URL=https://api.example.com
LOG_CHANNEL=stderr

DB_CONNECTION=mariadb
DB_HOST=prod-cafe-db.abcdefghijkl.ap-southeast-1.rds.amazonaws.com
DB_PORT=3306
DB_DATABASE=cafe_api
DB_USERNAME=cafe_app
DB_PASSWORD=replace-me

OCTANE_SERVER=frankenphp
```

Optional when using a separate users database:

```env
USERS_DB_HOST=prod-users-db.abcdefghijkl.ap-southeast-1.rds.amazonaws.com
USERS_DB_PORT=3306
USERS_DB_DATABASE=cafe_users
USERS_DB_USERNAME=cafe_users_app
USERS_DB_PASSWORD=replace-me
```

Optional when using cookie-based SPA auth:

```env
SESSION_DOMAIN=.example.com
SANCTUM_STATEFUL_DOMAINS=app.example.com,api.example.com
```

## Post-Deployment Checklist

After the first AWS deployment, verify:

1. the App Runner service is healthy
2. `GET /up` returns success
3. `GET /api/ping` returns `pong`
4. the app can connect to the production database
5. migrations ran against the intended database
6. logs are visible in App Runner or CloudWatch
7. if using Sanctum cookies, login and authenticated requests work from the production frontend origin

## References

- AWS App Runner custom domains: https://docs.aws.amazon.com/apprunner/latest/dg/manage-custom-domains.html
- AWS App Runner VPC networking: https://docs.aws.amazon.com/apprunner/latest/dg/network-vpc.html
- Amazon RDS MariaDB endpoint and port: https://docs.aws.amazon.com/AmazonRDS/latest/UserGuide/USER_ConnectToMariaDBInstance.EndpointAndPort.html
