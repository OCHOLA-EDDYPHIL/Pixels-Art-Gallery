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
    exit('Method not allowed.');
}

if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    exit('Invalid CSRF token');
}

if (!isset($_SESSION['email'])) {
    header('Location: ../index.php');
    exit("You must be logged in to upload an image.");
}

if (!isset($_FILES['fileToUpload'])) {
    exit("No file or caption provided.");
}

$caption = $_POST['caption'] ?? 'No cap';
$email = $_SESSION['email'];

$config = Container::config();
$uploadDir = __DIR__ . '/../uploads';
$service = new ImageService(Container::db(), $config, $uploadDir);
$result = $service->upload($_FILES['fileToUpload'], $caption, $email);

if ($result['success'] === false) {
    echo $result['message'];
    exit();
}

header('Location: ../main.php');
exit();
