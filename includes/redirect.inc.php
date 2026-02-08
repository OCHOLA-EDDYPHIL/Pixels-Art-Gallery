<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Container;
use App\Services\UrlService;

// Check if 'c' parameter is set in the URL
if (isset($_GET['c'])) {
    $shortCode = $_GET['c']; // The short code passed in the URL
    $urlShortener = new UrlService(Container::db());

    // Retrieve the long URL from the database using the short code
    $longUrl = $urlShortener->resolve($shortCode);

    // Redirect to the long URL if found
    if ($longUrl) {
        header("Location: " . $longUrl); // Redirect to the long URL
        exit();
    } else {
        echo "URL not found."; // Display an error message if the URL is not found
    }
}
