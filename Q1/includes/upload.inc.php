<?php
echo "<pre>POST: ";
print_r($_POST);
echo "</pre>";

echo "<pre>FILES: ";
print_r($_FILES);
echo "</pre>";

session_start();
if (!isset($_SESSION['email'])) {
    // Redirect back or show an error message
    echo "You must be logged in to upload an image.";
    header('Location: ../index.php');
    exit();
}
require_once '../Classes/Databasehandler.php';
require_once '../Classes/ImageUploader.php';

if (isset($_POST['submit']) && isset($_FILES['fileToUpload'])) {
    $dbHandler = new Databasehandler();
    $imageUploader = new ImageUploader($dbHandler);

    $file = $_FILES['fileToUpload'];
    $caption = isset($_POST['caption']) ? $_POST['caption'] : 'No cap'; // Default to an empty string if caption is not set
    $email = $_SESSION['email']; // Assuming the user is logged in and the email is stored in the session
    $userId = $dbHandler->getUserIdByEmail($email); // Get the user ID from the email

    if (!$userId) {
        // Redirect back or show an error message
        echo "User not found.";
        exit();
    }

    $uploadResult = $imageUploader->handleUpload($file, $caption, $userId);

    if (is_string($uploadResult)) {
        // Handle error
        echo $uploadResult;
    } else {
        // Redirect or inform the user of success
        echo "Image uploaded successfully!";
    }
} else {
    // Redirect back or show an error message
    echo "No file or caption provided.";
}