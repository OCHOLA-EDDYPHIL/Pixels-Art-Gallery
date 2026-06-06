<?php

declare(strict_types=1);

namespace App\Controllers;

abstract class BaseController
{
    /**
     * @param array<string, mixed> $data
     */
    protected function render(string $template, array $data = []): string
    {
        extract($data, EXTR_SKIP);

        ob_start();
        require dirname(__DIR__) . '/Views/' . $template . '.php';
        return (string) ob_get_clean();
    }
}
