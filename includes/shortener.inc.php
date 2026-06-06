<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/session_config.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/../config.php';

use App\Container;
use App\Services\UrlService;
use App\Utils\Response;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::error('Method not allowed.', 405)->send();
}

if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    Response::error('Invalid CSRF token.', 403)->send();
}

$longUrl = trim($_POST['longUrl'] ?? '');
if ($longUrl === '') {
    Response::error('URL is required.', 400)->send();
}

$urlShortener = new UrlService(Container::db());
$shortCode = $urlShortener->shorten($longUrl);
if (!preg_match('/^[a-f0-9]{6}$/i', $shortCode)) {
    Response::error($shortCode, 400)->send();
}

$baseurl = getBaseUrl();
$shortUrl = htmlspecialchars($baseurl . 'redirect.inc.php?c=' . $shortCode, ENT_QUOTES, 'UTF-8');
$content = sprintf('Short URL: <a href="%s">%s</a>', $shortUrl, $shortUrl);

Response::html($content)->send();
