<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/session_config.php';
require_once __DIR__ . '/csrf.php';

use App\Container;
use App\Services\ImageService;
use App\Utils\Response;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::error('Method not allowed.', 405)->send();
}

if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    Response::error('Invalid CSRF token.', 403)->send();
}

if (!isset($_SESSION['email'])) {
    Response::error('You must be logged in to perform this action.', 401)->send();
}

$filename = $_POST['filename'] ?? '';
$filename = basename($filename);
if (!preg_match('/^[A-Za-z0-9._-]+$/', $filename)) {
    Response::error('Invalid filename.', 400)->send();
}

$config = Container::config();
$service = new ImageService(Container::db(), $config, __DIR__ . '/../uploads');
$result = $service->delete($filename, $_SESSION['email']);

Response::redirect('../main.php?message=' . urlencode($result))->send();
