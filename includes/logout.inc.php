<?php

declare(strict_types=1);

require_once __DIR__ . '/session_config.php';

use App\Utils\Csrf;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    exit('Invalid CSRF token');
}

$_SESSION = [];

if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

session_destroy();
session_regenerate_id(true);

header("Location: ../index.php");
exit();
