<?php

declare(strict_types=1);

namespace App\Auth\Fixture;

use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\User;
use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use App\Auth\Service\PasswordHasher;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;

class UserJoinConfirmFixture extends AbstractFixture
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
            new Email('join-wait-active@app.test'),
            $this->hasher->hash('new-PassworD-417'),
            new Token('00000000-0000-0000-0000-200000000001', new \DateTimeImmutable('+1 hours'))
        );

        $manager->persist($user);

        $user = User::requestJoinByEmail(
            Id::generate(),
            new \DateTimeImmutable('-1 hours'),
            new Email('join-wait-expired@app.test'),
            $this->hasher->hash('new-PassworD-291'),
            new Token('00000000-0000-0000-0000-200000000002', new \DateTimeImmutable('-1 hours'))
        );

        $manager->persist($user);

        $manager->flush();
    }
}
