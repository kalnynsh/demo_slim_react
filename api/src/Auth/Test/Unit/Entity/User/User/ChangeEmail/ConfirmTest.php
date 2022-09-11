<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User\ChangeEmail;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use App\Auth\Test\Builder\UserBuilder;
use DateInterval;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @covers \App\Auth\Entity\User\User::confirmEmailChanging
 *
 * @internal
 */
final class ConfirmTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();

        $now = new DateTimeImmutable();

        $token = $this->createToken($now->add(new DateInterval('P1D')));
        $user->requestEmailChanging($token, $now, $new = new Email('new-john@info.org'));

        self::assertNotNull($user->getNewEmailToken());

        $user->confirmEmailChanging($token->getValue(), $now->add(new DateInterval('PT2H')));

        self::assertNull($user->getNewEmail());
        self::assertEquals($new, $user->getEmail());
    }

    public function testInvalidToken(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->add(new DateInterval('P1D')));

        $user->requestEmailChanging($token, $now, new Email('new-john@info.org'));

        $this->expectExceptionMessage('Token is not valid.');
        $user->confirmEmailChanging('incorrect', $now->add(new DateInterval('PT2H')));
    }

    public function testExpiredToken(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now);

        $this->expectExceptionMessage('Token was expired.');
        $user->requestEmailChanging($token, $now, new Email('new-john@info.org'));
    }

    public function testNotRequested(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->add(new DateInterval('P1D')));

        $this->expectExceptionMessage('Changing was not requested.');
        $user->confirmEmailChanging($token->getValue(), $now->add(new DateInterval('PT2H')));
    }

    private function createToken(DateTimeImmutable $expires): Token
    {
        return new Token(
            Uuid::uuid4()->toString(),
            $expires
        );
    }
}
