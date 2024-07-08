<?php
// Initialize a new session or resume the existing one
session_start();

// Include the necessary classes for database and image handling
require_once '../Classes/Databasehandler.php';
require_once '../Classes/ImageHandler.php';

// Check if the user is logged in by verifying if their email is set in the session
if (!isset($_SESSION['email'])) {
    // Inform the user they must be logged in to perform the action and terminate the script
    echo "You must be logged in to perform this action.";
    exit;
}

// Ensure the request method is POST and the filename is provided
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filename'])) {
    // Retrieve the filename and user's email from the POST data and session respectively
    $filename = $_POST['filename'];
    $email = $_SESSION['email'];

    // Instantiate a new ImageHandler object
    $imageHandler = new ImageHandler();
    // Call the deleteImage method with the filename and email, and store the result
    $result = $imageHandler->deleteImage($filename, $email);

    // Redirect the user back to main.php with a message indicating the result of the deletion
    header('Location: ../main.php?message=' . urlencode($result));
} else {
    // Inform the user of an invalid request if the conditions are not met
    echo "Invalid request.";
}