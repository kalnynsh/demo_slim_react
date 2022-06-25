<?php

declare(strict_types=1);

use Slim\App;
use App\Http\Middleware;
use Middlewares\ContentType;
use Middlewares\ContentLanguage;
use Slim\Middleware\ErrorMiddleware;
use App\FeatureToggle\FeaturesMiddleware;

return static function (App $app): void {
    $app->add(Middleware\DomainExceptionHandler::class);
    $app->add(Middleware\ValidationExceptionHandler::class);
    $app->add(FeaturesMiddleware::class);
    $app->add(Middleware\InputTrimmerMiddleware::class);

    $app->add(Middleware\TranslatorLocaleMiddleware::class);
    $app->add(ContentType::class);
    $app->add(ContentLanguage::class);
    $app->addBodyParsingMiddleware();

    $app->add(ErrorMiddleware::class);
};
