<?php

declare(strict_types=1);

use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Monolog\Handler\StreamHandler;
use Psr\Container\ContainerInterface;
use Monolog\Processor\ProcessorInterface;
use App\FeatureToggle\FeaturesMonologProcessor;

return [
    LoggerInterface::class => static function (ContainerInterface $container): Logger {
        /**
         * @psalm-suppress MixedArrayAccess
         * @psalm-var array{
         *   debug:bool,
         *   file:string,
         *   stderr:bool,
         *   processors:string[]
         * } $config
         */
        $config = $container->get('config')['logger'];

        $level = $config['debug'] ? Logger::DEBUG : Logger::INFO;

        $mLogger = new Logger('API');

        if ($config['stderr']) {
            $mLogger->pushHandler(new StreamHandler('php://stderr', $level));
        }

        if (! empty($config['file'])) {
            $mLogger->pushHandler(new StreamHandler($config['file'], $level));
        }

        foreach ($config['processors'] as $class) {
            /** @var ProcessorInterface $processor */
            $processor = $container->get($class);
            $mLogger->pushProcessor($processor);
        }

        return $mLogger;
    },

    'config' => [
        'logger' => [
            'debug' => (bool) getenv('APP_DEBUG'),
            'file' => null,
            'stderr' => true,
            'processors' => [
                FeaturesMonologProcessor::class,
            ],
        ],
    ],
];
