<?php

declare(strict_types=1);

use App\Console\FixturesLoadCommand;
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

        /** @var Loader $loader */
        $loader = $container->get(Loader::class);

        /** @var ORMExecutor $executor */
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
            ],
            'fixture_paths' => [
                __DIR__  . '/../../src/Auth/Fixture',
            ],
        ],
    ],
];
