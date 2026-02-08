<?php

declare(strict_types=1);

namespace App\Utils;

final class Response
{
    public static function json(array $data, int $code = 200): never
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public static function error(string $message, int $code = 400): never
    {
        self::json(['error' => $message], $code);
    }

    public static function redirect(string $location): never
    {
        header("Location: {$location}");
        exit;
    }
}
