<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\UserRepository;
use App\Utils\Session;
use App\Utils\Validator;

final class AuthService
{
    public function __construct(private readonly UserRepository $users)
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

        if ($this->users->findByEmail($email) !== null) {
            $errors[] = 'Email already registered';
        }

        if (!empty($errors)) {
            return $errors;
        }

        $this->users->createUser($email, password_hash($password, PASSWORD_DEFAULT));

        return [];
    }

    /**
     * @return string[] errors
     */
    public function login(string $email, string $password): array
    {
        $user = $this->users->findByEmail($email);

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
}
