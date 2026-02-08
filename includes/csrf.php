<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Utils\Csrf;

function generateCsrfToken(): string
{
    return Csrf::token();
}

function verifyCsrfToken(string $token): bool
{
    return Csrf::verify($token);
}
