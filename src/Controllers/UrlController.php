<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Utils\Csrf;
use App\Utils\Response;
use App\Utils\Session;

final class UrlController extends BaseController
{
    /**
     * @param \Closure(): \App\Services\UrlService $urlFactory
     */
    public function __construct(private readonly \Closure $urlFactory)
    {
    }

    public function create(): Response
    {
        Session::start();
        return Response::html($this->render('url/create', ['csrfToken' => Csrf::token()]));
    }

    /**
     * @param array<string, mixed> $input
     */
    public function store(array $input, string $baseUrl): Response
    {
        Session::start();
        if (!Csrf::verify((string) ($input['csrf_token'] ?? ''))) {
            return Response::text('Invalid CSRF token', 403);
        }

        if (empty($input['longUrl'])) {
            return Response::text('URL is required', 400);
        }

        $shortCode = ($this->urlFactory)()->shorten((string) $input['longUrl']);
        if (!preg_match('/^[a-f0-9]{6}$/i', $shortCode)) {
            return Response::text($shortCode, 400);
        }

        $shortUrl = rtrim($baseUrl, '/') . '/includes/redirect.inc.php?c=' . rawurlencode($shortCode);
        $escapedUrl = htmlspecialchars($shortUrl, ENT_QUOTES, 'UTF-8');

        return Response::html('Short URL: <a href="' . $escapedUrl . '">' . $escapedUrl . '</a>');
    }

    public function redirect(?string $shortCode): Response
    {
        if ($shortCode === null || $shortCode === '') {
            return Response::text('URL not found.', 404);
        }

        $longUrl = ($this->urlFactory)()->resolve($shortCode);
        if ($longUrl === null) {
            return Response::text('URL not found.', 404);
        }

        return Response::redirect($longUrl);
    }
}
