<?php

declare(strict_types=1);

namespace App\Logging;

use Closure;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

final class LazyLogger extends AbstractLogger
{
    private ?LoggerInterface $logger = null;
    private readonly Closure $factory;

    /**
     * @param callable(): LoggerInterface $factory
     */
    public function __construct(callable $factory)
    {
        $this->factory = Closure::fromCallable($factory);
    }

    public function log($level, $message, array $context = []): void
    {
        if ($this->logger === null) {
            $this->logger = ($this->factory)();
        }

        $this->logger->log($level, $message, $context);
    }
}
