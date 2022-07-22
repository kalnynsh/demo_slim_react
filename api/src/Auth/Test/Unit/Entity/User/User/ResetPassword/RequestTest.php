<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User\ResetPassword;

use App\Auth\Entity\User\Token;
use App\Auth\Test\Builder\UserBuilder;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @covers \App\Auth\Entity\User\User::requestPasswordReset
 */
class RequestTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();

        $now = new \DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 hour'));
        $user->requestPasswordReset($token, $now);

        $newDate = $now->modify('+1 hour');
        $newToken = $this->createToken($newDate->modify('+2 hour'));
        $user->requestPasswordReset($newToken, $newDate);

        self::assertEquals($newToken, $user->getPasswordResetToken());
    }

    public function testNotActive(): void
    {
        $user = (new UserBuilder())->build();

        $now = new \DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 hour'));

        $this->expectExceptionMessage('User is not active.');
        $user->requestPasswordReset($token, $now);
    }

    private function createToken(\DateTimeImmutable $expires): Token
    {
        return new Token(
            Uuid::uuid4()->toString(),
            $expires
        );
    }
}
