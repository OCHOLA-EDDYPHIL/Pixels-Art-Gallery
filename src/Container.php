<?php

declare(strict_types=1);

namespace App;

use App\Config\Config;
use App\Database\Connection;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PDO;
use Psr\Log\LoggerInterface;

final class Container
{
    private static ?Config $config = null;
    private static ?PDO $db = null;
    private static ?LoggerInterface $logger = null;

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

    public static function logger(): LoggerInterface
    {
        if (self::$logger === null) {
            $logDir = dirname(__DIR__, 1) . '/storage/logs';
            if (!is_dir($logDir)) {
                mkdir($logDir, 0775, true);
            }

            $logger = new Logger('pixels');
            $logger->pushHandler(new StreamHandler($logDir . '/app.log'));
            self::$logger = $logger;
        }

        return self::$logger;
    }
}
