<?php

declare(strict_types=1);

namespace App\Auth\Query\FindIdentityById;

use App\Http\Middleware\Auth\Identity;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;

final class Fetcher
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function fetch(string $id): ?Identity
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        /** @var Result $stmt */
        $stmt = $queryBuilder
            ->select(['id', 'role'])
            ->from('auth_users')
            ->where('id = ' . $queryBuilder->createNamedParameter($id))
            ->executeQuery();

        /** @var array{id:string, role:string}|false */
        $row = $stmt->fetchAssociative();

        if ($row === false) {
            return null;
        }

        return new Identity(
            id: $row['id'],
            role: $row['role']
        );
    }
}
