<?php

declare(strict_types=1);

namespace App;

use App\Config\Config;
use App\Database\Connection;
use App\Logging\LazyLogger;
use Monolog\Handler\ErrorLogHandler;
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
            self::$logger = new LazyLogger(fn (): LoggerInterface => self::createLogger());
        }

        return self::$logger;
    }

    private static function createLogger(): LoggerInterface
    {
        $logger = new Logger('app');
        $rootPath = dirname(__DIR__, 1);
        $logPath = $rootPath . DIRECTORY_SEPARATOR . 'logs';

        if (is_dir($logPath) || (is_writable($rootPath) && mkdir($logPath, 0775, true))) {
            $logger->pushHandler(new StreamHandler($logPath . DIRECTORY_SEPARATOR . 'app.log'));
        } else {
            $logger->pushHandler(new ErrorLogHandler());
        }

        return $logger;
    }
}
