<?php
session_start();
require_once '../Classes/Databasehandler.php';
require_once '../Classes/ImageHandler.php';

if (!isset($_SESSION['email'])) {
    echo "You must be logged in to perform this action.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filename'])) {
    $filename = $_POST['filename'];
    $email = $_SESSION['email'];

    $imageHandler = new ImageHandler();
    $result = $imageHandler->deleteImage($filename, $email);

    // Redirect back to main.php with a message
    header('Location: ../main.php?message=' . urlencode($result));
} else {
    echo "Invalid request.";
}