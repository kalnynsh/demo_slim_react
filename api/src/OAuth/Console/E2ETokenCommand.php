<?php

declare(strict_types=1);

namespace App\OAuth\Console;

use App\OAuth\Entity\AccessToken;
use App\OAuth\Entity\Scope;
use DateTimeImmutable;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

final class E2ETokenCommand extends Command
{
    private const NO_ERROR = 0;
    private const HAS_ERROR = 1;

    private string $privateKeyPath;
    private ClientRepositoryInterface $clients;

    public function __construct(
        string $privateKeyPath,
        ClientRepositoryInterface $clients
    ) {
        parent::__construct();
        $this->privateKeyPath = $privateKeyPath;
        $this->clients = $clients;
    }

    protected function configure(): void
    {
        $this
            ->setName('oauth:e2e-token')
            ->setDescription('Generate auth token for E2E-tests')
            ->addArgument('client-id', InputArgument::OPTIONAL)
            ->addArgument('scopes', InputArgument::OPTIONAL)
            ->addArgument('user-id', InputArgument::OPTIONAL)
            ->addArgument('role', InputArgument::OPTIONAL);
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        while (empty($input->getArgument('client-id'))) {
            $input->setArgument(
                'client-id',
                (string)$helper->ask($input, $output, new Question('Client (frontend): ', 'frontend'))
            );
        }

        while (empty($input->getArgument('scopes'))) {
            $input->setArgument(
                'scopes',
                (string)$helper->ask($input, $output, new Question('Scopes (common): ', 'common'))
            );
        }

        while (empty($input->getArgument('user-id'))) {
            $input->setArgument(
                'user-id',
                (string)$helper->ask($input, $output, new Question('User ID: '))
            );
        }

        while (empty($input->getArgument('role'))) {
            $input->setArgument(
                'role',
                (string)$helper->ask($input, $output, new Question('Role (user): ', 'user'))
            );
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var sting $clientId */
        $clientId = $input->getArgument('client-id');

        /** @var sting $scopes */
        $scopes = $input->getArgument('scopes');

        /** @var sting $userId */
        $userId = $input->getArgument('user-id');

        /** @var sting $role */
        $role = $input->getArgument('role');

        $client = $this->clients->getClientEntity($clientId);

        if ($client === null) {
            $output->writeln('<error>Invalid client ' . $clientId . '</error>');

            return self::HAS_ERROR;
        }

        $token = new AccessToken(
            $client,
            array_map(static fn (string $name) => new Scope($name), explode(' ', $scopes))
        );

        $token->setIdentifier(bin2hex(random_bytes(40)));
        $token->setExpiryDateTime(new DateTimeImmutable('+1000 years'));
        $token->setUserIdentifier($userId);
        $token->setUserRole($role);

        $token->setPrivateKey(new CryptKey($this->privateKeyPath, null, false));

        $output->writeln((string)$token);

        return self::NO_ERROR;
    }
}
