<?php

declare(strict_types=1);

use App\FeatureToggle\FeatureFlagTwigExtension;
use App\Frontend\FrontendUrlTwigExtension;
use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Extension\ExtensionInterface;
use Twig\Loader\FilesystemLoader;

return [
    Environment::class => static function (ContainerInterface $container): Environment {
        /**
         * @psalm-suppress MixedAssignment
         * @var array{
         *   debug:bool,
         *   template_dirs:array<string,string>,
         *   cache_dir:string,
         *   extensions:array<int,string>
         * } $config
         */
        $config = $container->get('config')['twig'];

        $loader = new FilesystemLoader();

        foreach ($config['template_dirs'] as $alias => $dir) {
            $loader->addPath($dir, $alias);
        }

        /** @psalm-suppress MixedArrayAccess */
        $environment = new Environment(
            $loader,
            [
                'cache' => $config['debug'] ? false : $config['cache_dir'],
                'debug' => $config['debug'],
                'strict_variables' => $config['debug'],
                'auto_load' => $config['debug'],
            ]
        );

        /** @psalm-suppress MixedArrayAccess */
        if ($config['debug']) {
            $environment->addExtension(new DebugExtension());
        }

        /** @psalm-suppress MixedArrayAccess */
        foreach ($config['extensions'] as $class) {
            /** @var ExtensionInterface $extension */
            $extension = $container->get($class);
            $environment->addExtension($extension);
        }

        return $environment;
    },

    'config' => [
        'twig' => [
            'debug' => (bool) getenv('APP_DEBUG'),
            'template_dirs' => [
                FilesystemLoader::MAIN_NAMESPACE => __DIR__ . '/../../templates',
            ],
            'cache_dir' => __DIR__ . '/../../var/cache/twig',
            'extensions' => [
                FrontendUrlTwigExtension::class,
                FeatureFlagTwigExtension::class,
            ],
        ],
    ],
];
