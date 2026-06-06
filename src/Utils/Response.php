<?php

declare(strict_types=1);

namespace App\Utils;

final class Response
{
    /**
     * @param array<string, string> $headers
     */
    private function __construct(
        private readonly string $content = '',
        private readonly int $statusCode = 200,
        private readonly array $headers = []
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function json(array $data, int $code = 200): self
    {
        return new self(
            (string) json_encode($data, JSON_THROW_ON_ERROR),
            $code,
            ['Content-Type' => 'application/json']
        );
    }

    public static function html(string $content, int $code = 200): self
    {
        return new self($content, $code, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    public static function error(string $message, int $code = 400): self
    {
        return self::html(
            sprintf(
                '<!doctype html><html lang="en"><head><meta charset="UTF-8"><title>Error</title></head>'
                . '<body><h1>%s</h1><p>%s</p></body></html>',
                htmlspecialchars(self::statusText($code), ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($message, ENT_QUOTES, 'UTF-8')
            ),
            $code
        );
    }

    public static function redirect(string $location, int $code = 302): self
    {
        return new self('', $code, ['Location' => $location]);
    }

    public function send(): never
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header(sprintf('%s: %s', $name, $value));
        }

        echo $this->content;
        exit;
    }

    private static function statusText(int $code): string
    {
        return match ($code) {
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
            default => 'Error',
        };
    }
}
