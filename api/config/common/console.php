<?php

declare(strict_types=1);

use Symfony\Component\Console\Command\ListCommand;
use Doctrine\Migrations\Tools\Console\Command\LatestCommand;
use Doctrine\Migrations\Tools\Console\Command\RollupCommand;
use Doctrine\Migrations\Tools\Console\Command\StatusCommand;
use Doctrine\Migrations\Tools\Console\Command\CurrentCommand;
use Doctrine\Migrations\Tools\Console\Command\ExecuteCommand;
use Doctrine\Migrations\Tools\Console\Command\MigrateCommand;
use Doctrine\Migrations\Tools\Console\Command\VersionCommand;
use Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand;
use Doctrine\Migrations\Tools\Console\Command\GenerateCommand;
use Doctrine\Migrations\Tools\Console\Command\UpToDateCommand;
use Doctrine\Migrations\Tools\Console\Command\SyncMetadataCommand;

return [
    'config' => [
        'console' => [
            'commands' => [
                ValidateSchemaCommand::class,
                CurrentCommand::class,
                ExecuteCommand::class,
                GenerateCommand::class,
                LatestCommand::class,
                MigrateCommand::class,
                RollupCommand::class,
                StatusCommand::class,
                VersionCommand::class,
                UpToDateCommand::class,
                SyncMetadataCommand::class,
                ListCommand::class,
            ],
        ],
    ],
];
