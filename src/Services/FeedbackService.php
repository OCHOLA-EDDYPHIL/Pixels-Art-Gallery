<?php

declare(strict_types=1);

namespace App\Services;

use PDO;

/**
 * Stores and retrieves visitor feedback.
 *
 * Added for CNS 3205 Lab 2 (Part B #7): a second form that both POSTs a new
 * row and reads existing rows back out, proving round-trip database access.
 */
final class FeedbackService
{
    public function __construct(private readonly PDO $db)
    {
    }

    /**
     * Validate and persist one feedback entry.
     *
     * @return string[] validation errors (empty on success)
     */
    public function submit(string $name, string $email, string $message): array
    {
        $errors = [];

        $name = trim($name);
        $message = trim($message);
        $email = trim($email);

        if ($name === '') {
            $errors[] = 'Name is required';
        } elseif (mb_strlen($name) > 100) {
            $errors[] = 'Name must be 100 characters or fewer';
        }

        if ($message === '') {
            $errors[] = 'Message is required';
        }

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email address is not valid';
        }

        if (!empty($errors)) {
            return $errors;
        }

        $stmt = $this->db->prepare(
            'INSERT INTO feedback (name, email, message) VALUES (:name, :email, :message)'
        );
        $stmt->execute([
            ':name' => $name,
            ':email' => $email !== '' ? $email : null,
            ':message' => $message,
        ]);

        return [];
    }

    /**
     * Most recent feedback first.
     *
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        $stmt = $this->db->query(
            'SELECT name, email, message, created_at FROM feedback ORDER BY created_at DESC, id DESC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
