<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Container;
use App\Services\UrlService;
use App\Utils\Response;

$shortCode = $_GET['c'] ?? '';
if ($shortCode === '') {
    Response::error('Short code is required.', 400)->send();
}

$urlShortener = new UrlService(Container::db());
$longUrl = $urlShortener->resolve($shortCode);

if ($longUrl === null) {
    Response::error('URL not found.', 404)->send();
}

Response::redirect($longUrl)->send();
