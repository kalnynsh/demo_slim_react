<?php

declare(strict_types=1);

use App\Console\FixturesLoadCommand;
use App\OAuth\Console\E2ETokenCommand;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\ORM\Tools\Console\Command\SchemaTool;
use Psr\Container\ContainerInterface;

return [
    FixturesLoadCommand::class => static function (ContainerInterface $container) {
        /**
         * @psalm-suppress MixedArrayAccess
         * @var array{commands:string[],fixture_paths:string[]} $config
         */
        $config = $container->get('config')['console'];

        $loader = $container->get(Loader::class);

        $executor = $container->get(ORMExecutor::class);

        return new FixturesLoadCommand(
            $loader,
            $executor,
            $config['fixture_paths']
        );
    },

    'config' => [
        'console' => [
            'commands' => [
                FixturesLoadCommand::class,
                SchemaTool\DropCommand::class,
                E2ETokenCommand::class,
            ],
            'fixture_paths' => [
                __DIR__ . '/../../src/Auth/Fixture',
            ],
        ],
    ],
];
