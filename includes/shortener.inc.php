<?php

// Include the necessary configuration and class files
require_once __DIR__ . '/../config.php'; // Include the configuration file
require_once __DIR__ . '/../Classes/Databasehandler.php'; // Include the Databasehandler class for database operations
require_once __DIR__ . '/../Classes/Urlshortener.php'; // Include the Urlshortener class for URL shortening functionality

// Check if the form was submitted via POST and the long URL field is not empty
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['longUrl'])) {
    // Get a singleton instance of Databasehandler with database configuration parameters
    $dbHandler = Databasehandler::getInstance(DB_HOST, DB_NAME, DB_USER, DB_PASS);
    // Establish a PDO database connection
    $pdo = $dbHandler->connect();
    // Create a new Urlshortener object with the PDO object
    $urlShortener = new Urlshortener($pdo);
    // Call the shortenURL method to generate a short code for the provided long URL
    $shortCode = $urlShortener->shortenURL($_POST['longUrl']);
    // Retrieve the base URL of the application
    $baseurl = getBaseUrl(); // Use the function to get the base URL
    // Display the generated short URL as a clickable link
    echo "Short URL: <a href=\"{$baseurl}redirect.inc.php?c=$shortCode\">{$baseurl}redirect.inc.php?c={$shortCode}</a>";
}