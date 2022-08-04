<?php

declare(strict_types=1);

namespace App\Auth\Query\FindIdByCredentials;

use App\Auth\Entity\User\Status;
use App\Auth\Service\PasswordHasher;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;

final class Fetcher
{
    private Connection $connection;
    private PasswordHasher $hasher;

    public function __construct(Connection $connection, PasswordHasher $hasher)
    {
        $this->connection = $connection;
        $this->hasher = $hasher;
    }

    public function fetch(Query $query): ?User
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $emailInLow = mb_strtolower($query->email);
        $emailParameter = $queryBuilder->createNamedParameter($emailInLow);

        /** @var Result $stmt */
        $stmt = $queryBuilder
            ->select([
                'id',
                'status',
                'password_hash',
            ])
            ->from('auth_users')
            ->where('email = ' . $emailParameter)
            ->executeQuery();

        /**
         * @var array{
         *   id: string,
         *   status: string,
         *   password_hash: ?string,
         * }|false
         */
        $row = $stmt->fetchAssociative();

        if ($row === false) {
            return null;
        }

        $hash = $row['password_hash'] ?? '';

        if (!$this->hasher->validate($query->password, $hash)) {
            return null;
        }

        return new User(
            id: $row['id'],
            isActive: $row['status'] === Status::ACTIVE
        );
    }
}
