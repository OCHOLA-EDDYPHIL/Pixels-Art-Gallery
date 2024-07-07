<?php
session_start();
require_once 'Classes/Databasehandler.php';

if (!isset($_SESSION['email'])) {
    echo "You must be logged in to perform this action.";
    exit;
}

$filename = isset($_GET['filename']) ? $_GET['filename'] : 'Does not exist';
$email = $_SESSION['email'];

$dbHandler = Databasehandler::getInstance();
$pdo = $dbHandler->connect();

// Verify that the user is the uploader
$stmt = $pdo->prepare("SELECT * FROM photos WHERE filename = ? AND user_id = ?");
$stmt->execute([$filename, $email]);
$image = $stmt->fetch();

if ($image) {
    // Delete the file from the server
    unlink('uploads/' . $filename);
    // Delete the record from the database
    $stmt = $pdo->prepare("DELETE FROM photos WHERE filename = ?");
    $stmt->execute([$filename]);
    echo "Image deleted successfully.";
    header('Location: main.php'); // Redirect back to the gallery page
} else {
    echo "You do not have permission to delete this image or it does not exist.";
}
?>