<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Utils\Csrf;
use App\Utils\Response;
use App\Utils\Session;

final class AuthController extends BaseController
{
    /**
     * @param \Closure(): \App\Services\AuthService $authFactory
     */
    public function __construct(private readonly \Closure $authFactory)
    {
    }

    public function showLogin(): Response
    {
        Session::start();
        if (isset($_SESSION['email'])) {
            return Response::redirect('/main.php');
        }

        $loginErrors = $_SESSION['login_errors'] ?? [];
        $signupErrors = $_SESSION['signup_errors'] ?? [];
        unset($_SESSION['login_errors'], $_SESSION['signup_errors']);

        return Response::html($this->render('auth/login', [
            'csrfToken' => Csrf::token(),
            'loginErrors' => $loginErrors,
            'signupErrors' => $signupErrors,
        ]));
    }

    /**
     * @param array<string, mixed> $input
     */
    public function login(array $input): Response
    {
        Session::start();
        if (!Csrf::verify((string) ($input['csrf_token'] ?? ''))) {
            return Response::text('Invalid CSRF token', 403);
        }

        $errors = ($this->authFactory)()->login(trim((string) ($input['email'] ?? '')), (string) ($input['pwd'] ?? ''));
        if (!empty($errors)) {
            $_SESSION['login_errors'] = $errors;
            return Response::redirect('/index.php');
        }

        return Response::redirect('/main.php');
    }

    /**
     * @param array<string, mixed> $input
     */
    public function signup(array $input): Response
    {
        Session::start();
        if (!Csrf::verify((string) ($input['csrf_token'] ?? ''))) {
            return Response::text('Invalid CSRF token', 403);
        }

        $errors = ($this->authFactory)()->signup(trim((string) ($input['email'] ?? '')), (string) ($input['pwd'] ?? ''));
        if (!empty($errors)) {
            $_SESSION['signup_errors'] = $errors;
            return Response::redirect('/index.php');
        }

        $_SESSION['signup_success'] = 'true';
        return Response::redirect('/index.php');
    }

    /**
     * @param array<string, mixed> $input
     */
    public function logout(array $input): Response
    {
        if (!Csrf::verify((string) ($input['csrf_token'] ?? ''))) {
            return Response::text('Invalid CSRF token', 403);
        }

        ($this->authFactory)()->logout();
        return Response::redirect('/index.php');
    }
}
