<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User;

use App\Auth\Entity\User\Role;
use App\Auth\Test\Builder\UserBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Auth\Entity\User\User::changeRole
 *
 * @internal
 */
class ChangeRoleTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())
            ->build();

        $user->changeRole(
            $newRole = new Role(Role::ADMIN)
        );

        self::assertEquals($newRole, $user->getRole());
    }
}
