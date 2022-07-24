<?php

declare(strict_types=1);

use App\FeatureToggle\FeaturesMonologProcessor;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\ProcessorInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

use function App\env;

return [
    LoggerInterface::class => static function (ContainerInterface $container): Logger {
        /**
         * @psalm-suppress MixedArrayAccess
         * @var array{
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

        if (!empty($config['file'])) {
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
            'debug' => (bool)env('APP_DEBUG', '0'),
            'file' => null,
            'stderr' => true,
            'processors' => [
                FeaturesMonologProcessor::class,
            ],
        ],
    ],
];
