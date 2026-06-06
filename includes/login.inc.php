<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/session_config.php';
require_once __DIR__ . '/csrf.php';

use App\Container;
use App\Services\AuthService;
use App\Utils\Response;

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    Response::error('Method not allowed.', 405)->send();
}

if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    Response::error('Invalid CSRF token.', 403)->send();
}

$email = trim($_POST['email'] ?? '');
$pwd = $_POST['pwd'] ?? '';

$auth = new AuthService(Container::db());
$errors = $auth->login($email, $pwd);

if (!empty($errors)) {
    $_SESSION['login_errors'] = $errors;
    Response::redirect('../index.php')->send();
}

Response::redirect('../main.php')->send();
