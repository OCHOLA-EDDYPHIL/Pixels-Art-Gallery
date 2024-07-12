<?php

require_once __DIR__ . '/../Classes/Databasehandler.php';
require_once __DIR__ . '/../Classes/Urlshortener.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['longUrl'])) {
    $dbHandler = Databasehandler::getInstance();
    $pdo = $dbHandler->connect();
    $urlShortener = new Urlshortener($pdo);
    $shortCode = $urlShortener->shortenURL($_POST['longUrl']);
    // Ensure the base URL ends with a slash
    $baseurl = rtrim($_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']), '/') . '/';
    // Correctly append 'redirect.inc.php?c=' to the base URL
    echo "Short URL: <a href=\"http://{$baseurl}redirect.inc.php?c={$shortCode}\">http://{$baseurl}redirect.inc.php?c={$shortCode}</a>";
}