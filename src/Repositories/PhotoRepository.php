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
     * @return list<array{filename: string, caption: string, user_id: string}>
     */
    public function allPhotos(): array
    {
        $stmt = $this->db->query('SELECT filename, caption, user_id FROM photos');

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createPhoto(string $filename, string $caption, string $userId): void
    {
        $stmt = $this->db->prepare('INSERT INTO photos (filename, caption, user_id) VALUES (?, ?, ?)');
        $stmt->execute([$filename, $caption, $userId]);
    }

    /**
     * @return array{filename: string}|null
     */
    public function findOwnedPhoto(string $filename, string $userId): ?array
    {
        $stmt = $this->db->prepare('SELECT filename FROM photos WHERE filename = ? AND user_id = ?');
        $stmt->execute([$filename, $userId]);
        $photo = $stmt->fetch(PDO::FETCH_ASSOC);

        return $photo === false ? null : $photo;
    }

    public function deleteOwnedPhoto(string $filename, string $userId): bool
    {
        $stmt = $this->db->prepare('DELETE FROM photos WHERE filename = ? AND user_id = ?');
        $stmt->execute([$filename, $userId]);

        return $stmt->rowCount() > 0;
    }
}
