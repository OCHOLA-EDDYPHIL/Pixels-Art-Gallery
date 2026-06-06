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
    Response::redirect('../index.php')->send();
}

if (!isset($_FILES['fileToUpload'])) {
    Response::error('No file or caption provided.', 400)->send();
}

$caption = $_POST['caption'] ?? 'No cap';
$email = $_SESSION['email'];

$config = Container::config();
$uploadDir = __DIR__ . '/../uploads';
$service = new ImageService(Container::db(), $config, $uploadDir);
$result = $service->upload($_FILES['fileToUpload'], $caption, $email);

if ($result['success'] === false) {
    Response::error($result['message'], 400)->send();
}

Response::redirect('../main.php')->send();
