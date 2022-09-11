#!/usr/bin/env php
<?php

declare(strict_types=1);

use Symfony\Component\Console\Application;

use function App\env;

require __DIR__ . '/../vendor/autoload.php';

if ($dsn = env('SENTRY_DSN')) {
    \Sentry\init(['dsn' => $dsn]);
}

/** @var Psr\Container\ContainerInterface $container */
$container = require __DIR__ . '/../config/container.php';

$cli = new Application('Console', '1.0.0 (stable)');

$cli->setCatchExceptions(true);

if (getenv('SENTRY_DSN')) {
    $cli->setCatchExceptions(false);
}

/**
 * @var string[] $commands
 * @psalm-suppress MixedArrayAccess
 */
$commands = $container->get('config')['console']['commands'];

foreach ($commands as $name) {
    /** @var \Symfony\Component\Console\Command\Command $command */
    $command = $container->get($name);
    $cli->add($command);
}

$cli->run();
