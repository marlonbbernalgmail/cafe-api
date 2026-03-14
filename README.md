# Cafe API

Laravel 12 API starter optimized for a Docker-first workflow and AWS App Runner deployment.

## Stack

- Laravel 12
- Laravel Octane
- FrankenPHP
- MariaDB for local development
- GitHub Actions for CI/CD
- Amazon ECR + AWS App Runner for deployment

## Local development

1. Copy `.env.example` to `.env`.
2. Generate an application key:

```sh
php artisan key:generate
```

3. Start the local stack:

```sh
docker compose up --build
```

4. Run migrations:

```sh
docker compose exec app php artisan migrate --force
```

The API will be available at `http://localhost:8000`.

## Useful endpoints

- `GET /` basic JSON status
- `GET /up` Laravel health check
- `GET /api/ping` API smoke test

## AWS deployment flow

Pushes to `main` trigger `.github/workflows/deploy.yml`, which:

1. installs dependencies and runs tests
2. builds the production Docker image
3. runs migrations when the required database secrets are available
4. pushes the image to Amazon ECR
5. relies on AWS App Runner auto-deployment from the `main` image tag

## Required GitHub configuration

Repository variables:

- `AWS_REGION`
- `ECR_REPOSITORY`
- `APP_URL`

Repository secrets:

- `AWS_ROLE_TO_ASSUME`
- `APP_KEY`
- `DB_HOST`
- `DB_PORT`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`

## AWS setup notes

- Create an ECR repository and point App Runner to the `main` tag.
- Keep App Runner and ECR in the same AWS account and region for auto-deployments.
- Enable automatic deployments on the App Runner service.
- Store runtime secrets in App Runner or another AWS secrets source.
- Use RDS MariaDB once you move past local development.
- If your RDS instance is private, GitHub-hosted runners will not be able to run migrations against it without additional network access.
