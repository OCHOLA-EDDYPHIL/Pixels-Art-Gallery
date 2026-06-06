<?php

declare(strict_types=1);

require_once __DIR__ . '/session_config.php';
require_once __DIR__ . '/csrf.php';

use App\Utils\Response;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::error('Method not allowed.', 405)->send();
}

if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    Response::error('Invalid CSRF token.', 403)->send();
}

$_SESSION = [];

if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

session_destroy();
session_regenerate_id(true);

Response::redirect('../index.php')->send();
