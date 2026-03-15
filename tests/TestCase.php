<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use PDO;
use PDOException;
use RuntimeException;
use Throwable;

abstract class TestCase extends BaseTestCase
{
    private static bool $usersTestingDatabasePrepared = false;

    protected function setUp(): void
    {
        if (! self::$usersTestingDatabasePrepared) {
            $this->ensureUsersTestingDatabaseExists();
            self::$usersTestingDatabasePrepared = true;
        }

        parent::setUp();
    }

    private function ensureUsersTestingDatabaseExists(): void
    {
        $host = getenv('USERS_DB_HOST') ?: '127.0.0.1';
        $port = getenv('USERS_DB_PORT') ?: '3306';
        $database = getenv('USERS_DB_DATABASE') ?: 'cafe_api_test';
        $username = getenv('USERS_DB_USERNAME') ?: 'root';
        $password = getenv('USERS_DB_PASSWORD') ?: '';

        if ($this->canConnectToUsersTestingDatabase($host, $port, $database, $username, $password)) {
            return;
        }

        if (! preg_match('/\A[A-Za-z0-9_]+\z/', $database) || ! preg_match('/\A[A-Za-z0-9_]+\z/', $username)) {
            throw new RuntimeException('PHPUnit database bootstrap only supports alphanumeric database and username values.');
        }

        $sql = sprintf(
            "CREATE DATABASE IF NOT EXISTS %s CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; GRANT ALL PRIVILEGES ON %s.* TO '%s'@'%%' IDENTIFIED BY '%s'; FLUSH PRIVILEGES;",
            $database,
            $database,
            str_replace("'", "\\'", $username),
            str_replace("'", "\\'", $password),
        );
        $command = [
            'docker',
            'compose',
            'exec',
            '-T',
            'db',
            'sh',
            '-lc',
            sprintf(
                'mariadb -uroot -p"$MARIADB_ROOT_PASSWORD" -e "%s"',
                addcslashes($sql, "\\\"$"),
            ),
        ];
        $descriptorSpec = [
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];
        $process = proc_open($command, $descriptorSpec, $pipes, dirname(__DIR__));

        if (! is_resource($process)) {
            throw new RuntimeException(
                sprintf(
                    'Unable to start docker compose while preparing the PHPUnit users test database [%s]. Ensure Docker is installed and the local database service is available.',
                    $database,
                ),
            );
        }

        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        if ($exitCode !== 0) {
            throw new RuntimeException(
                sprintf(
                    "Unable to prepare the PHPUnit users test database [%s] through docker compose.\nstdout: %s\nstderr: %s",
                    $database,
                    trim($stdout),
                    trim($stderr),
                ),
            );
        }
    }

    private function canConnectToUsersTestingDatabase(
        string $host,
        string $port,
        string $database,
        string $username,
        string $password,
    ): bool {
        try {
            new PDO(
                "mysql:host={$host};port={$port};dbname={$database}",
                $username,
                $password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
            );

            return true;
        } catch (PDOException) {
            return false;
        } catch (Throwable $exception) {
            throw new RuntimeException(
                sprintf(
                    'Unexpected error while checking connectivity to the PHPUnit users test database [%s] at %s:%s.',
                    $database,
                    $host,
                    $port,
                ),
                previous: $exception,
            );
        }
    }
}
