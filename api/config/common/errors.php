<?php

declare(strict_types=1);

use App\ErrorHandler\LoggedErrorHandler;
use App\ErrorHandler\SentryErrorHandlerDecorator;
use App\Sentry\Sentry;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Middleware\ErrorMiddleware;

use function App\env;

return [
    ErrorMiddleware::class => static function (ContainerInterface $container): ErrorMiddleware {
        $callableResolver = $container->get(CallableResolverInterface::class);

        $responseFactory = $container->get(ResponseFactoryInterface::class);

        /**
         * @psalm-suppress MixedArrayAccess
         * @var array{
         *  display_error_details:bool,
         *  use_sentry:bool
         * } $config
         */
        $config = $container->get('config')['errors'];

        $middleware =  new ErrorMiddleware(
            $callableResolver,
            $responseFactory,
            $config['display_error_details'],
            true,
            true
        );

        $logger = $container->get(LoggerInterface::class);

        $loggedErrorHandler = new LoggedErrorHandler(
            $callableResolver,
            $responseFactory,
            $logger
        );

        if ($config['use_sentry']) {
            $middleware->setDefaultErrorHandler(
                new SentryErrorHandlerDecorator(
                    $loggedErrorHandler,
                    $container->get(Sentry::class)
                )
            );
        }

        if (!$config['use_sentry']) {
            $middleware->setDefaultErrorHandler($loggedErrorHandler);
        }

        return $middleware;
    },

    'config' => [
        'errors' => [
            'display_error_details' => (bool)env('APP_DEBUG'),
            'use_sentry' => (bool)env('SENTRY_DSN'),
        ],
    ],
];
