<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Utils\Csrf;
use App\Utils\Response;
use App\Utils\Session;
use PDO;
use PDOException;

final class GalleryController extends BaseController
{
    public function __construct(private readonly PDO $db)
    {
    }

    public function index(): Response
    {
        Session::start();
        $error = null;
        $images = [];

        try {
            $stmt = $this->db->query('SELECT filename, caption, user_id FROM photos');
            $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            $error = 'Error: ' . $exception->getMessage();
        }

        return Response::html($this->render('gallery/index', [
            'csrfToken' => Csrf::token(),
            'currentUserEmail' => $_SESSION['email'] ?? null,
            'images' => $images,
            'error' => $error,
        ]));
    }
}
