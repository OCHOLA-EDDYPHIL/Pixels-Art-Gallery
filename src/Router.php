<?php

declare(strict_types=1);

namespace App;

use App\Utils\Response;

final class Router
{
    /**
     * @var array<string, array<string, callable(): Response>>
     */
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    public function add(string $method, string $path, callable $handler): void
    {
        $this->routes[strtoupper($method)][$this->normalize($path)] = $handler;
    }

    public function dispatch(string $method, string $uri): Response
    {
        $path = $this->normalize((string) parse_url($uri, PHP_URL_PATH));
        $method = strtoupper($method);

        if (isset($this->routes[$method][$path])) {
            return ($this->routes[$method][$path])();
        }

        foreach ($this->routes as $routeMethod => $routes) {
            if ($routeMethod !== $method && isset($routes[$path])) {
                return Response::text('Method not allowed', 405)->withHeaders(['Allow' => $routeMethod]);
            }
        }

        return Response::text('Not found', 404);
    }

    private function normalize(string $path): string
    {
        $path = '/' . ltrim($path, '/');
        return rtrim($path, '/') ?: '/';
    }
}
