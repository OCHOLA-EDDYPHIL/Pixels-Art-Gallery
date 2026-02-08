<?php

declare(strict_types=1);

namespace App\Config;

use Dotenv\Dotenv;

final class Config
{
    private array $config;

    public function __construct(string $basePath)
    {
        if (class_exists(Dotenv::class)) {
            Dotenv::createImmutable($basePath)->safeLoad();
        }

        $this->config = [
            'db_host' => $_ENV['DB_HOST'] ?? 'localhost',
            'db_name' => $_ENV['DB_NAME'] ?? 'project',
            'db_user' => $_ENV['DB_USER'] ?? 'root',
            'db_pass' => $_ENV['DB_PASS'] ?? '',
            'app_env' => $_ENV['APP_ENV'] ?? 'development',
            'app_url' => $_ENV['APP_URL'] ?? 'http://localhost:8000',
            'max_file_size' => (int)($_ENV['MAX_FILE_SIZE'] ?? 10485760),
            'allowed_extensions' => $_ENV['ALLOWED_EXTENSIONS'] ?? 'jpg,png',
        ];
    }

    public function dbDsn(): string
    {
        return sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8mb4',
            $this->config['db_host'],
            $this->config['db_name']
        );
    }

    public function dbUser(): string
    {
        return $this->config['db_user'];
    }

    public function dbPass(): string
    {
        return $this->config['db_pass'];
    }

    public function appUrl(): string
    {
        return rtrim($this->config['app_url'], '/');
    }

    public function appEnv(): string
    {
        return $this->config['app_env'];
    }

    public function maxFileSize(): int
    {
        return $this->config['max_file_size'];
    }

    /**
     * @return string[]
     */
    public function allowedExtensions(): array
    {
        $parts = array_map('trim', explode(',', $this->config['allowed_extensions']));
        return array_values(array_filter($parts));
    }
}
