<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use DomainException;

final class UserRepository
{
    private EntityManagerInterface $em;
    /**
     * @var EntityRepository<User>
     */
    private EntityRepository $repository;

    /**
     * @param EntityRepository<User> $repository
     */
    public function __construct(EntityManagerInterface $em, EntityRepository $repository)
    {
        $this->em = $em;
        $this->repository = $repository;
    }

    /**
     * @throws DomainException
     */
    public function get(Id $userId): User
    {
        /** @var User|null */
        $user = $this->repository->find($userId->getValue());

        if ($user === null) {
            throw new DomainException('User was not found.');
        }

        return $user;
    }

    /**
     * @throws DomainException
     */
    public function getByEmail(Email $email): User
    {
        /** @var User|null */
        $user = $this
            ->repository
            ->findOneBy(['email' => $email->getValue()]);

        if ($user === null) {
            throw new DomainException('User was not found.');
        }

        return $user;
    }

    /**
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     */
    public function findByJoinConfirmToken(string $tokenValue): ?User
    {
        return $this
            ->repository
            ->findOneBy(
                [
                    'joinConfirmToken.value' => $tokenValue,
                ]
            );
    }

    /**
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     */
    public function findByNewEmailToken(string $tokenValue): ?User
    {
        return $this
            ->repository
            ->findOneBy(
                [
                    'newEmailToken.value' => $tokenValue,
                ]
            );
    }

    /**
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     */
    public function findByPasswordResetToken(string $tokenValue): ?User
    {
        return $this
            ->repository
            ->findOneBy(
                [
                    'passwordResetToken.value' => $tokenValue,
                ]
            );
    }

    public function hasByEmail(Email $email): bool
    {
        return $this
            ->repository
            ->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->andWhere('t.email = :email')
            ->setParameter(':email', $email->getValue())
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }


    public function hasByNetwork(Network $network): bool
    {
        return $this
            ->repository
            ->createQueryBuilder('t')
            ->innerJoin('t.networks', 'n')
            ->andWhere('n.network.name = :name and n.network.identity = :identity')
            ->setParameter(':name', $network->getName())
            ->setParameter(':identity', $network->getIdentity())
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }

    public function add(User $user): void
    {
        $this->em->persist($user);
    }

    public function remove(User $user): void
    {
        $this->em->remove($user);
    }
}
