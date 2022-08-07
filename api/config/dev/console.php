<?php

declare(strict_types=1);

use App\Console\FixturesLoadCommand;
use App\OAuth\Console\E2ETokenCommand;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\ORM\Tools\Console\Command\SchemaTool;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
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
    E2ETokenCommand::class => static function (ContainerInterface $container) {
        /**
         * @psalm-suppress MixedArrayAccess
         * @var array{private_key_path:string} $config
         */
        $config = $container->get('config')['oauth'];

        return new E2ETokenCommand(
            $config['private_key_path'],
            $container->get(ClientRepositoryInterface::class)
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
