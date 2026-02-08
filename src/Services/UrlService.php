<?php

declare(strict_types=1);

namespace App\Services;

use App\Utils\Validator;
use PDO;
use RuntimeException;

final class UrlService
{
    public function __construct(private readonly PDO $db)
    {
    }

    public function shorten(string $url): string
    {
        if (!Validator::url($url)) {
            return 'Invalid URL';
        }

        $code = $this->generateCode();
        $stmt = $this->db->prepare('INSERT INTO urls (long_url, short_code) VALUES (?, ?)');
        $stmt->execute([$url, $code]);

        return $code;
    }

    public function resolve(string $code): ?string
    {
        $stmt = $this->db->prepare('SELECT long_url FROM urls WHERE short_code = ?');
        $stmt->execute([$code]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['long_url'] ?? null;
    }

    private function generateCode(): string
    {
        $attempts = 0;
        do {
            $attempts++;
            $code = substr(bin2hex(random_bytes(4)), 0, 6);
            $stmt = $this->db->prepare('SELECT 1 FROM urls WHERE short_code = ?');
            $stmt->execute([$code]);
        } while ($stmt->fetchColumn() && $attempts < 5);

        if ($attempts >= 5) {
            throw new RuntimeException('Failed to generate unique short code');
        }

        return $code;
    }
}
