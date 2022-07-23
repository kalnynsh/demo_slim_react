<?php

declare(strict_types=1);

namespace App\Auth\Fixture;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Token;
use App\Auth\Entity\User\User;
use App\Auth\Service\PasswordHasher;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class UserJoinFixture extends AbstractFixture
{
    private PasswordHasher $hasher;

    public function __construct()
    {
        $this->hasher = new PasswordHasher();
    }

    public function load(ObjectManager $manager): void
    {
        $user = User::requestJoinByEmail(
            Id::generate(),
            new \DateTimeImmutable('-1 hours'),
            new Email('join-existing@app.test'),
            $this->hasher->hash('new-PassworD-716'),
            new Token('00000000-0000-0000-0000-100000000002', new \DateTimeImmutable('+1 hours'))
        );

        $manager->persist($user);
        $manager->flush();
    }
}
