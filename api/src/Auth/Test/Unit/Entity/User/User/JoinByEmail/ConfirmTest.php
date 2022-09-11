<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User\JoinByEmail;

use App\Auth\Entity\User\Token;
use App\Auth\Test\Builder\UserBuilder;
use DateInterval;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @covers \App\Auth\Entity\User\User::confirmJoin
 *
 * @internal
 */
final class ConfirmTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())
            ->withJoinConfirmToken($token = $this->createToken())
            ->build();

        self::assertTrue($user->isWait());
        self::assertFalse($user->isActive());

        /** @psalm-suppress PossiblyFalseArgument */
        $user->confirmJoin(
            $token->getValue(),
            $token->getExpires()->sub(new DateInterval('P1D'))
        );

        self::assertTrue($user->isActive());
        self::assertFalse($user->isWait());

        self::assertNull($user->getJoinConfirmToken());
    }

    public function testWrong(): void
    {
        $user = (new UserBuilder())
            ->withJoinConfirmToken($token = $this->createToken())
            ->build();

        $this->expectExceptionMessage('Token is not valid.');

        /** @psalm-suppress PossiblyFalseArgument */
        $user->confirmJoin(
            Uuid::uuid4()->toString(),
            $token->getExpires()->sub(new DateInterval('P1D'))
        );
    }

    public function testExpired(): void
    {
        $user = (new UserBuilder())
            ->withJoinConfirmToken($token = $this->createToken())
            ->build();

        $this->expectExceptionMessage('Token was expired.');

        $user->confirmJoin(
            $token->getValue(),
            $token->getExpires()->add(new DateInterval('P1D'))
        );
    }

    private function createToken(): Token
    {
        return new Token(
            Uuid::uuid4()->toString(),
            new DateTimeImmutable('+1 day')
        );
    }
}
