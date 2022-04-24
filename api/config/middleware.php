<?php

declare(strict_types=1);

use Slim\App;
use App\Http\Middleware;
use Middlewares\ContentLanguage;
use Middlewares\ContentType;
use Slim\Middleware\ErrorMiddleware;

return static function (App $app): void {
    $app->add(Middleware\DomainExceptionHandler::class);
    $app->add(Middleware\ValidationExceptionHandler::class);
    $app->add(Middleware\InputTrimmerMiddleware::class);

    $app->add(Middleware\TranslatorLocaleMiddleware::class);
    $app->add(ContentType::class);
    $app->add(ContentLanguage::class);
    $app->addBodyParsingMiddleware();

    $app->add(ErrorMiddleware::class);
};
