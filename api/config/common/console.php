<?php

declare(strict_types=1);

use Doctrine\Migrations;
use Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand;

return [
    'config' => [
        'console' => [
            'commands' => [
                ValidateSchemaCommand::class,

                Migrations\Tools\Console\Command\CurrentCommand::class,
                Migrations\Tools\Console\Command\ExecuteCommand::class,
                Migrations\Tools\Console\Command\GenerateCommand::class,
                Migrations\Tools\Console\Command\LatestCommand::class,
                Migrations\Tools\Console\Command\MigrateCommand::class,
                Migrations\Tools\Console\Command\RollupCommand::class,
                Migrations\Tools\Console\Command\StatusCommand::class,
                Migrations\Tools\Console\Command\VersionCommand::class,
                Migrations\Tools\Console\Command\UpToDateCommand::class,
                Migrations\Tools\Console\Command\DiffCommand::class,
                Migrations\Tools\Console\Command\ListCommand::class,
            ],
        ],
    ],
];
