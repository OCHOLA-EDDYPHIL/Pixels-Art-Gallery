<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class UserRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    /**
     * @return array{email_address: string, pwd: string}|null
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT email_address, pwd FROM users WHERE email_address = :email');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user === false ? null : $user;
    }

    public function createUser(string $email, string $passwordHash): void
    {
        $stmt = $this->db->prepare('INSERT INTO users (email_address, pwd) VALUES (:email, :pwd)');
        $stmt->execute([
            ':email' => $email,
            ':pwd' => $passwordHash,
        ]);
    }
}
