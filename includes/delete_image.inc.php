<?php

require_once __DIR__ . '/session_config.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/../Classes/Databasehandler.php';
require_once __DIR__ . '/../Classes/ImageHandler.php';

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
if (!preg_match('/^[a-f0-9]{32}\.(jpg|jpeg|png)$/i', $filename)) {
    // Fallback for legacy filenames: strip path and validate characters
    $filename = basename($filename);
    if (!preg_match('/^[A-Za-z0-9._-]+$/', $filename)) {
        http_response_code(400);
        exit('Invalid filename.');
    }
}

$email = $_SESSION['email'];

$imageHandler = new ImageHandler();
$result = $imageHandler->deleteImage($filename, $email);

header('Location: ../main.php?message=' . urlencode($result));
exit();
