<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/session_config.php';
require_once __DIR__ . '/csrf.php';

use App\Config\Config;
use App\Container;
use App\Services\ImageService;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Invalid request method.');
}

if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    exit('Invalid CSRF token');
}

if (!isset($_SESSION['email'])) {
    http_response_code(401);
    exit('You must be logged in to perform this action.');
}

$filename = $_POST['filename'] ?? '';
$filename = basename($filename);
if (!preg_match('/^[A-Za-z0-9._-]+$/', $filename)) {
    http_response_code(400);
    exit('Invalid filename.');
}

$config = Container::config();
$service = new ImageService(Container::db(), $config, __DIR__ . '/../uploads');
$result = $service->delete($filename, $_SESSION['email']);

header('Location: ../main.php?message=' . urlencode($result));
exit();
