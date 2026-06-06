<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\UrlRepository;
use App\Utils\Validator;
use RuntimeException;

final class UrlService
{
    public function __construct(private readonly UrlRepository $urls)
    {
    }

    public function shorten(string $url): string
    {
        if (!Validator::url($url)) {
            return 'Invalid URL';
        }

        $code = $this->generateCode();
        $this->urls->createShortUrl($url, $code);

        return $code;
    }

    public function resolve(string $code): ?string
    {
        return $this->urls->findLongUrlByCode($code);
    }

    private function generateCode(): string
    {
        for ($attempts = 0; $attempts < 5; $attempts++) {
            $code = substr(bin2hex(random_bytes(4)), 0, 6);

            if (!$this->urls->shortCodeExists($code)) {
                return $code;
            }
        }

        throw new RuntimeException('Failed to generate unique short code');
    }
}
