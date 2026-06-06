<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class PhotoRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    /**
     * @return array<int, array{filename: string, caption: string|null, user_id: string}>
     */
    public function all(): array
    {
        $stmt = $this->db->query('SELECT filename, caption, user_id FROM photos');

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
