<?php

declare(strict_types=1);

namespace App\Services;

use App\Utils\Session;
use App\Utils\Validator;
use PDO;

final class AuthService
{
    public function __construct(private readonly PDO $db)
    {
    }

    /**
     * @return string[] errors
     */
    public function signup(string $email, string $password, string $username = ''): array
    {
        $errors = [];

        if (!Validator::email($email)) {
            $errors[] = 'Invalid email address';
        }

        $username = trim($username);
        if ($username !== '' && mb_strlen($username) > 50) {
            $errors[] = 'Username must be 50 characters or fewer';
        }

        $pwErrors = Validator::password($password);
        if (!empty($pwErrors)) {
            $errors = array_merge($errors, $pwErrors);
        }

        if ($this->emailExists($email)) {
            $errors[] = 'Email already registered';
        }

        if (!empty($errors)) {
            return $errors;
        }

        $stmt = $this->db->prepare(
            'INSERT INTO users (username, email_address, pwd) VALUES (:username, :email, :pwd)'
        );
        $stmt->execute([
            ':username' => $username !== '' ? $username : null,
            ':email' => $email,
            ':pwd' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        return [];
    }

    /**
     * @return string[] errors
     */
    public function login(string $email, string $password): array
    {
        $stmt = $this->db->prepare('SELECT pwd FROM users WHERE email_address = :email');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['pwd'])) {
            return ['Invalid email or password'];
        }

        Session::start();
        Session::regenerate();
        $_SESSION['email'] = $email;

        return [];
    }

    public function logout(): void
    {
        Session::destroy();
    }

    private function emailExists(string $email): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM users WHERE email_address = :email');
        $stmt->execute([':email' => $email]);
        return (bool) $stmt->fetchColumn();
    }
}
