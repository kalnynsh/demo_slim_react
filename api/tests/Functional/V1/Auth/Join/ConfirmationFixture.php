<?php

declare(strict_types=1);

namespace Test\Functional\V1\Auth\Join;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Token;
use App\Auth\Entity\User\User;
use DateInterval;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class ConfirmationFixture extends AbstractFixture
{
    public const VALID = '00000000-0000-0000-0000-000000000001';
    public const EXPIRED = '00000000-0000-0000-0000-000000000002';

    public function load(ObjectManager $manager): void
    {
        // Valid
        $user = User::requestJoinByEmail(
            Id::generate(),
            $date = new DateTimeImmutable(),
            new Email('valid-name@test.org'),
            'password-hash',
            new Token(self::VALID, $date->add(new DateInterval('PT1H')))
        );

        $manager->persist($user);

        // Expired
        /** @psalm-suppress PossiblyFalseArgument */
        $user = User::requestJoinByEmail(
            Id::generate(),
            $date = new DateTimeImmutable(),
            new Email('expired-name@test.org'),
            'password-hash',
            new Token(self::EXPIRED, $date->sub(new DateInterval('PT2H')))
        );

        $manager->persist($user);

        $manager->flush();
    }
}
