<?php

declare(strict_types=1);

namespace Test\Functional\V1\Auth;

use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Role;
use App\Auth\Test\Builder\UserBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class UserFixture extends AbstractFixture
{
    public const USER_ADMIN_ID = '00000000-0000-0000-0000-000000000004';
    public const ROLE_ADMIN = Role::ADMIN;
    public const USER_ID = '00000000-0000-0000-0000-000000000005';
    public const ROLE_USER = Role::USER;

    public function load(ObjectManager $manager): void
    {
        $user = (new UserBuilder())
            ->withId(new Id(self::USER_ADMIN_ID))
            ->withRole(new Role(self::ROLE_ADMIN))
            ->active()
            ->build();

        $manager->persist($user);

        $user = (new UserBuilder())
            ->withId(new Id(self::USER_ID))
            ->active()
            ->build();

        $manager->persist($user);

        $manager->flush();
    }
}
