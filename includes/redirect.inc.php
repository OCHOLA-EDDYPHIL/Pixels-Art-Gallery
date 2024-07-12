<?php

require_once __DIR__.'/../Classes/Databasehandler.php';
require_once __DIR__.'/../Classes/Urlshortener.php';

// Check if 'c' parameter is set in the URL
if (isset($_GET['c'])) {
    $shortCode = $_GET['c'];

    $dbHandler = Databasehandler::getInstance();
    $pdo = $dbHandler->connect();
    $urlShortener = new Urlshortener($pdo);

    // Retrieve the long URL from the database using the short code
    $longUrl = $urlShortener->getLongURL($shortCode);

    // Redirect to the long URL if found
    if ($longUrl) {
        header("Location: " . $longUrl);
        exit();
    } else {
        echo "URL not found.";
    }
}