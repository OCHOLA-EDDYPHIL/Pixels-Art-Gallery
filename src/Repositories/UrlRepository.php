<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class UrlRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    public function createShortUrl(string $longUrl, string $shortCode): void
    {
        $stmt = $this->db->prepare('INSERT INTO urls (long_url, short_code) VALUES (?, ?)');
        $stmt->execute([$longUrl, $shortCode]);
    }

    public function findLongUrlByCode(string $shortCode): ?string
    {
        $stmt = $this->db->prepare('SELECT long_url FROM urls WHERE short_code = ?');
        $stmt->execute([$shortCode]);
        $longUrl = $stmt->fetchColumn();

        return $longUrl === false ? null : (string) $longUrl;
    }

    public function shortCodeExists(string $shortCode): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM urls WHERE short_code = ?');
        $stmt->execute([$shortCode]);

        return (bool) $stmt->fetchColumn();
    }
}
