<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/session_config.php';

use App\Utils\Csrf;
use App\Container;
use App\Services\AuthService;

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit('Method not allowed');
}

if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    exit('Invalid CSRF token');
}

$email = trim($_POST['email'] ?? '');
$pwd = $_POST['pwd'] ?? '';

$auth = new AuthService(Container::db());
$errors = $auth->signup($email, $pwd);

if (!empty($errors)) {
    $_SESSION['signup_errors'] = $errors;
    header("Location: ../index.php");
    exit();
}

$_SESSION['signup_success'] = 'true';
header("Location: ../index.php");
exit();
