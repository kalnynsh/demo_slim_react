<?php

declare(strict_types=1);

namespace Test\Functional\V1\Auth;

use App\Auth\Entity\User\Id;
use App\Auth\Test\Builder\UserBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class UserFixture extends AbstractFixture
{
    public const USER_ID = '00000000-0000-0000-0000-000000000004';

    public function load(ObjectManager $manager): void
    {
        $user = (new UserBuilder())
            ->withId(new Id(self::USER_ID))
            ->active()
            ->build();

        $manager->persist($user);

        $manager->flush();
    }
}
