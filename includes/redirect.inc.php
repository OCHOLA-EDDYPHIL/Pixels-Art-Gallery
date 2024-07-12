<?php

require_once __DIR__.'/../Classes/Databasehandler.php';
require_once __DIR__.'/../Classes/Urlshortener.php';

// Check if 'c' parameter is set in the URL
if (isset($_GET['c'])) {
    $shortCode = $_GET['c']; // The short code passed in the URL

    $dbHandler = Databasehandler::getInstance(); // Get an instance of the Databasehandler
    $pdo = $dbHandler->connect(); // Establish a database connection
    $urlShortener = new Urlshortener($pdo); // Create a new Urlshortener object

    // Retrieve the long URL from the database using the short code
    $longUrl = $urlShortener->getLongURL($shortCode);

    // Redirect to the long URL if found
    if ($longUrl) {
        header("Location: " . $longUrl); // Redirect to the long URL
        exit();
    } else {
        echo "URL not found."; // Display an error message if the URL is not found
    }
}