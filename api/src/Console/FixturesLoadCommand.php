<?php

declare(strict_types=1);

namespace App\Console;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class FixturesLoadCommand extends Command
{
    private const NO_ERRORS = 0;

    private Loader $loader;
    private ORMExecutor $executor;

    /**
     * @var string[]
     */
    private array $paths;

    /**
     * @param string[] $paths
     */
    public function __construct(
        Loader $loader,
        ORMExecutor $executor,
        array $paths
    ) {
        parent::__construct();

        $this->loader   = $loader;
        $this->executor = $executor;
        $this->paths    = $paths;
    }

    protected function configure(): void
    {
        $this
            ->setName('fixtures:load')
            ->setDescription('Load fixtures');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<comment>Load fixtures</comment>');

        foreach ($this->paths as $path) {
            $this
                ->loader
                ->loadFromDirectory($path);
        }

        $this
            ->executor
            ->setLogger(static function (string $message) use ($output): void {
                $output->writeln($message);
            });

        $this
            ->executor
            ->execute($this->loader->getFixtures());

        $output->writeln('<info>Done!</info>');

        return self::NO_ERRORS;
    }
}
