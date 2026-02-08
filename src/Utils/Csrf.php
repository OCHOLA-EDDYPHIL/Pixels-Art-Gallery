<?php

declare(strict_types=1);

namespace App\Utils;

final class Csrf
{
    private const SESSION_KEY = 'csrf_token';

    public static function token(): string
    {
        Session::start();
        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::SESSION_KEY];
    }

    public static function verify(string $token): bool
    {
        Session::start();
        return isset($_SESSION[self::SESSION_KEY]) && hash_equals($_SESSION[self::SESSION_KEY], $token);
    }
}
