<?php

declare(strict_types=1);

namespace App\OAuth\Console;

use App\OAuth\Command\ClearExpiredItems\Command;
use App\OAuth\Command\ClearExpiredItems\Handler;
use Symfony\Component\Console\Command\Command as ConsoleCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ClearExpiredCommand extends ConsoleCommand
{
    private const NO_ERROR = 0;

    private Handler $handler;

    public function __construct(Handler $handler)
    {
        parent::__construct();
        $this->handler = $handler;
    }

    protected function configure(): void
    {
        $this
            ->setName('oauth:clear-expired')
            ->setDescription('Clear expired auth codes and refresh tokens');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->handler->handle(new Command(date(DATE_ATOM)));

        return self::NO_ERROR;
    }
}
