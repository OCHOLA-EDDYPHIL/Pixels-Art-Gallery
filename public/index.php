<?php

declare(strict_types=1);

use App\Container;
use App\Controllers\AuthController;
use App\Controllers\GalleryController;
use App\Controllers\UploadController;
use App\Controllers\UrlController;
use App\Router;
use App\Services\AuthService;
use App\Services\ImageService;
use App\Services\UrlService;
use App\Utils\Response;

$rootPath = dirname(__DIR__);
$autoload = $rootPath . '/vendor/autoload.php';

if (is_file($autoload)) {
    require_once $autoload;
} else {
    spl_autoload_register(static function (string $class) use ($rootPath): void {
        $prefix = 'App\\';
        if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
            return;
        }

        $relative = str_replace('\\', DIRECTORY_SEPARATOR, substr($class, strlen($prefix)));
        $file = $rootPath . '/src/' . $relative . '.php';
        if (is_file($file)) {
            require_once $file;
        }
    });
}

function baseUrl(): string
{
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);
    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    return $scheme . '://' . $host;
}

function staticResponse(string $rootPath, string $uri): ?Response
{
    $path = (string) parse_url($uri, PHP_URL_PATH);
    $prefixes = [
        '/assets/' => $rootPath . '/assets/',
        '/uploads/' => $rootPath . '/uploads/',
        '/images/' => $rootPath . '/images/',
    ];

    foreach ($prefixes as $prefix => $directory) {
        if (strncmp($path, $prefix, strlen($prefix)) !== 0) {
            continue;
        }

        $relative = ltrim(substr($path, strlen($prefix)), '/');
        $realDirectory = realpath($directory);
        $realFile = realpath($directory . $relative);
        if ($realDirectory === false || $realFile === false || strncmp($realFile, $realDirectory, strlen($realDirectory)) !== 0 || !is_file($realFile)) {
            return Response::text('Not found', 404);
        }

        $mimeType = mime_content_type($realFile) ?: 'application/octet-stream';
        return new Response((string) file_get_contents($realFile), 200, ['Content-Type' => $mimeType]);
    }

    return null;
}

$static = staticResponse($rootPath, $_SERVER['REQUEST_URI'] ?? '/');
if ($static !== null) {
    $static->send();
    return;
}

$authServiceFactory = fn (): AuthService => new AuthService(Container::db());
$imageServiceFactory = fn (): ImageService => new ImageService(Container::db(), Container::config(), $rootPath . '/uploads');
$urlServiceFactory = fn (): UrlService => new UrlService(Container::db());

$authController = new AuthController($authServiceFactory);
$galleryControllerFactory = fn (): GalleryController => new GalleryController(Container::db());
$uploadController = new UploadController($imageServiceFactory);
$urlController = new UrlController($urlServiceFactory);

$router = new Router();

$router->get('/', fn (): Response => $authController->showLogin());
$router->get('/index.php', fn (): Response => $authController->showLogin());
$router->post('/login', fn (): Response => $authController->login($_POST));
$router->post('/includes/login.inc.php', fn (): Response => $authController->login($_POST));
$router->post('/signup', fn (): Response => $authController->signup($_POST));
$router->post('/includes/signup.inc.php', fn (): Response => $authController->signup($_POST));
$router->post('/logout', fn (): Response => $authController->logout($_POST));
$router->post('/includes/logout.inc.php', fn (): Response => $authController->logout($_POST));

$router->get('/main.php', fn (): Response => $galleryControllerFactory()->index());
$router->get('/gallery', fn (): Response => $galleryControllerFactory()->index());

$router->get('/upload.php', fn (): Response => $uploadController->create());
$router->get('/upload', fn (): Response => $uploadController->create());
$router->post('/upload', fn (): Response => $uploadController->store($_POST, $_FILES));
$router->post('/includes/upload.inc.php', fn (): Response => $uploadController->store($_POST, $_FILES));
$router->post('/images/delete', fn (): Response => $uploadController->destroy($_POST));
$router->post('/includes/delete_image.inc.php', fn (): Response => $uploadController->destroy($_POST));

$router->get('/shorten.php', fn (): Response => $urlController->create());
$router->get('/shorten', fn (): Response => $urlController->create());
$router->post('/shorten', fn (): Response => $urlController->store($_POST, baseUrl()));
$router->post('/includes/shortener.inc.php', fn (): Response => $urlController->store($_POST, baseUrl()));
$router->get('/r', fn (): Response => $urlController->redirect($_GET['c'] ?? null));
$router->get('/includes/redirect.inc.php', fn (): Response => $urlController->redirect($_GET['c'] ?? null));

$response = $router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
$response->send();
