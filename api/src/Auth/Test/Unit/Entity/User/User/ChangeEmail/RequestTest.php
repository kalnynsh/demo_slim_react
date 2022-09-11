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
 * @covers \App\Auth\Entity\User\User::requestEmailChanging
 *
 * @internal
 */
final class RequestTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())
            ->withEmail($old = new Email('old-john@info.org'))
            ->active()
            ->build();

        $now = new DateTimeImmutable();

        $token = $this->createToken($now->add(new DateInterval('P1D')));
        $user->requestEmailChanging($token, $now, $new = new Email('new-john@info.org'));

        self::assertEquals($token, $user->getNewEmailToken());
        self::assertEquals($old, $user->getEmail());
        self::assertEquals($new, $user->getNewEmail());
    }

    public function testSame(): void
    {
        $user = (new UserBuilder())
            ->withEmail($old = new Email('old-john@info.org'))
            ->active()
            ->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->add(new DateInterval('P1D')));

        $this->expectExceptionMessage('New email equals old email.');
        $user->requestEmailChanging($token, $now, $old);
    }

    public function testAlready(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->add(new DateInterval('P1D')));

        $user->requestEmailChanging($token, $now, $new = new Email('new-john@info.org'));

        $this->expectExceptionMessage('Email changing was already requested.');

        $user->requestEmailChanging($token, $now->add(new DateInterval('PT1H')), $new);
    }

    public function testExpired(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->add(new DateInterval('PT1H')));

        $user->requestEmailChanging($token, $now, new Email('tmp-johns@info.org'));

        $newDate = $now->add(new DateInterval('PT2H'));
        $newToken = $this->createToken($now->add(new DateInterval('PT1H')));

        $this->expectExceptionMessage('Token was expired.');
        $user->requestEmailChanging($newToken, $newDate, new Email('tom-johns@info.org'));
    }

    public function testNotActive(): void
    {
        $user = (new UserBuilder())->build();
        $now = new DateTimeImmutable();

        $token = $this->createToken($now->add(new DateInterval('PT1H')));

        $this->expectExceptionMessage('User is not active.');
        $user->requestEmailChanging($token, $now, new Email('johnny-depp@info.org'));
    }

    private function createToken(DateTimeImmutable $expires): Token
    {
        return new Token(
            Uuid::uuid4()->toString(),
            $expires
        );
    }
}
