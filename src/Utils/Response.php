<?php

declare(strict_types=1);

namespace App\Utils;

final class Response
{
    /**
     * @param array<string, string> $headers
     */
    public function __construct(
        private readonly string $body = '',
        private readonly int $statusCode = 200,
        private readonly array $headers = []
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function json(array $data, int $code = 200): self
    {
        return new self((string) json_encode($data), $code, ['Content-Type' => 'application/json']);
    }

    public static function error(string $message, int $code = 400): self
    {
        return self::json(['error' => $message], $code);
    }

    public static function html(string $body, int $code = 200): self
    {
        return new self($body, $code, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    public static function text(string $body, int $code = 200): self
    {
        return new self($body, $code, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    public static function redirect(string $location, int $code = 302): self
    {
        return new self('', $code, ['Location' => $location]);
    }

    /**
     * @param array<string, string> $headers
     */
    public function withHeaders(array $headers): self
    {
        return new self($this->body, $this->statusCode, array_merge($this->headers, $headers));
    }

    public function send(): void
    {
        http_response_code($this->statusCode);
        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value);
        }

        echo $this->body;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
}
