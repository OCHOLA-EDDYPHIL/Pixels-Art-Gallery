<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/session_config.php';
require_once __DIR__ . '/includes/csrf.php';

use App\Container;
use App\Controllers\GalleryController;
use App\Repositories\PhotoRepository;

$controller = new GalleryController(
    new PhotoRepository(Container::db()),
    __DIR__ . '/templates/gallery.php'
);

$controller->index();
