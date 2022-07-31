<?php

declare(strict_types=1);

namespace Test\Functional\OAuth;

use App\Auth\Entity\User\Email;
use App\Auth\Test\Builder\UserBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class AuthorizeFixture extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        $user = (new UserBuilder())
            ->withEmail(new Email('john-activater@test.org'))
            ->withPasswordHash()
            ->active()
            ->build();

        $manager->persist($user);

        $user = (new UserBuilder())
            ->withEmail(new Email('john-waiter@test.org'))
            ->withPasswordHash()
            ->build();

        $manager->persist($user);

        $manager->flush();
    }
}
