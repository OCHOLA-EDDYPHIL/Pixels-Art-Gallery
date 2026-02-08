<?php

require_once __DIR__ . '/session_config.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Classes/Databasehandler.php';
require_once __DIR__ . '/../Classes/Urlshortener.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        http_response_code(403);
        exit('Invalid CSRF token');
    }

    if (!empty($_POST['longUrl'])) {
        $dbHandler = Databasehandler::getInstance();
        $pdo = $dbHandler->connect();
        $urlShortener = new Urlshortener($pdo);
        $shortCode = $urlShortener->shortenURL($_POST['longUrl']);
        if (!preg_match('/^[a-f0-9]{6}$/i', $shortCode)) {
            http_response_code(400);
            echo htmlspecialchars($shortCode);
            exit();
        }
        $baseurl = getBaseUrl();
        echo "Short URL: <a href=\"{$baseurl}redirect.inc.php?c=$shortCode\">{$baseurl}redirect.inc.php?c={$shortCode}</a>";
    } else {
        http_response_code(400);
        echo 'URL is required';
    }
}
