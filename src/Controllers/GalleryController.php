<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\PhotoRepository;
use PDOException;

use function generateCsrfToken;

final class GalleryController
{
    public function __construct(
        private readonly PhotoRepository $photos,
        private readonly string $templatePath
    ) {
    }

    public function index(): void
    {
        $photos = [];
        $error = null;

        try {
            $photos = $this->photos->all();
        } catch (PDOException $e) {
            $error = 'Unable to load gallery photos.';
        }

        $loggedInEmail = $_SESSION['email'] ?? null;
        $csrfToken = generateCsrfToken();

        require $this->templatePath;
    }
}
