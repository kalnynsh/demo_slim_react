<?php

declare(strict_types=1);

use App\Sentry\Sentry;
use Sentry\SentrySdk;

return [
    Sentry::class => static fn (): Sentry => new Sentry(SentrySdk::getCurrentHub()),
];
