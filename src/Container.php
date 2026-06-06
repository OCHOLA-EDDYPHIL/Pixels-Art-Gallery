<?php

declare(strict_types=1);

namespace App;

use App\Config\Config;
use App\Database\Connection;
use App\Repositories\PhotoRepository;
use App\Repositories\UrlRepository;
use App\Repositories\UserRepository;
use PDO;

final class Container
{
    private static ?Config $config = null;
    private static ?PDO $db = null;

    public static function config(): Config
    {
        if (self::$config === null) {
            $rootPath = dirname(__DIR__, 1);
            self::$config = new Config($rootPath);
        }

        return self::$config;
    }

    public static function db(): PDO
    {
        if (self::$db === null) {
            self::$db = Connection::make(self::config());
        }

        return self::$db;
    }

    public static function users(): UserRepository
    {
        return new UserRepository(self::db());
    }

    public static function photos(): PhotoRepository
    {
        return new PhotoRepository(self::db());
    }

    public static function urls(): UrlRepository
    {
        return new UrlRepository(self::db());
    }
}
