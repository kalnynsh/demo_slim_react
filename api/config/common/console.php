<?php

declare(strict_types=1);

use Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand;

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
