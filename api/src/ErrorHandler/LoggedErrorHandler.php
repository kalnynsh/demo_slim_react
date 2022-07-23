<?php

declare(strict_types=1);

namespace App\ErrorHandler;

use Psr\Log\LoggerInterface;
use Slim\Handlers\ErrorHandler;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @property LoggerInterface $logger
 */
final class LoggedErrorHandler extends ErrorHandler
{
    /**
     * Ovewrite parent method.
     * Write to the error log with \Monolog\Logger
     *
     */
    protected function writeToErrorLog(): void
    {
        $this
            ->logger
            ->error(
                $this->exception->getMessage(),
                [
                    'exception' => $this->exception,
                    'url' => (string) $this->request->getUri(),
                ]
            );
    }
}
