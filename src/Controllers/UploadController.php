<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Utils\Csrf;
use App\Utils\Response;
use App\Utils\Session;

final class UploadController extends BaseController
{
    /**
     * @param \Closure(): \App\Services\ImageService $imageFactory
     */
    public function __construct(private readonly \Closure $imageFactory)
    {
    }

    public function create(): Response
    {
        Session::start();
        if (!isset($_SESSION['email'])) {
            return Response::redirect('/index.php');
        }

        return Response::html($this->render('upload/create', [
            'csrfToken' => Csrf::token(),
            'email' => $_SESSION['email'],
        ]));
    }

    /**
     * @param array<string, mixed> $input
     * @param array<string, mixed> $files
     */
    public function store(array $input, array $files): Response
    {
        Session::start();
        if (!Csrf::verify((string) ($input['csrf_token'] ?? ''))) {
            return Response::text('Invalid CSRF token', 403);
        }

        if (!isset($_SESSION['email'])) {
            return Response::redirect('/index.php');
        }

        if (!isset($files['fileToUpload'])) {
            return Response::text('No file or caption provided.', 400);
        }

        $result = ($this->imageFactory)()->upload($files['fileToUpload'], (string) ($input['caption'] ?? 'No cap'), $_SESSION['email']);
        if ($result['success'] === false) {
            return Response::text($result['message'], 400);
        }

        return Response::redirect('/main.php');
    }

    /**
     * @param array<string, mixed> $input
     */
    public function destroy(array $input): Response
    {
        Session::start();
        if (!Csrf::verify((string) ($input['csrf_token'] ?? ''))) {
            return Response::text('Invalid CSRF token', 403);
        }

        if (!isset($_SESSION['email'])) {
            return Response::text('You must be logged in to perform this action.', 401);
        }

        $filename = basename((string) ($input['filename'] ?? ''));
        if (!preg_match('/^[A-Za-z0-9._-]+$/', $filename)) {
            return Response::text('Invalid filename.', 400);
        }

        $message = ($this->imageFactory)()->delete($filename, $_SESSION['email']);
        return Response::redirect('/main.php?message=' . urlencode($message));
    }
}
