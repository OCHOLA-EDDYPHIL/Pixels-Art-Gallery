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
    public function signup(string $email, string $password): array
    {
        $errors = [];

        if (!Validator::email($email)) {
            $errors[] = 'Invalid email address';
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

        $stmt = $this->db->prepare('INSERT INTO users (email_address, pwd) VALUES (:email, :pwd)');
        $stmt->execute([
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
