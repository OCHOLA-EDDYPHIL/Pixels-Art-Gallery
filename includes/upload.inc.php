<?php
// Start a new session or resume the existing one
session_start();

// Check if the user's email is set in the session, redirect and exit if not
if (!isset($_SESSION['email'])) {
    header('Location: ../index.php');
    exit("You must be logged in to upload an image.");
}

// Include the necessary classes for database and image handling
require_once '../Classes/Databasehandler.php';
require_once '../Classes/ImageHandler.php';

// Ensure both the submit action and file upload are set, exit if not
if (!isset($_POST['submit'], $_FILES['fileToUpload'])) {
    exit("No file or caption provided.");
}

// Get an instance of the Databasehandler class
$dbHandler = Databasehandler::getInstance();

// Create a new ImageHandler object with the database handler
$imageUploader = new ImageHandler($dbHandler);

// Assign the uploaded file to a variable
$file = $_FILES['fileToUpload'];

// Assign the caption from POST data, default to 'No cap' if not set
$caption = $_POST['caption'] ?? 'No cap';

// Retrieve the user's email from the session
$email = $_SESSION['email'];

// Get the user ID associated with the email
$userId = $dbHandler->getUserEmail($email);

// Exit if the user ID was not found
if (!$userId) {
    exit("User not found.");
}

// Attempt to upload the image and assign the result to a variable
$uploadResult = $imageUploader->handleUpload($file, $caption, $userId);

// Check if the upload result is a string (error message) or success
if (is_string($uploadResult)) {
    echo $uploadResult;
} else {
    echo "Image uploaded successfully!";
}