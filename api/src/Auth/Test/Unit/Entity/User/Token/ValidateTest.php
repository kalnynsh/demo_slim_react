<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\Token;

use App\Auth\Entity\User\Token;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @covers \App\Auth\Entity\User\Token::validate
 *
 * @internal
 */
final class ValidateTest extends TestCase
{
    /**
     * @doesNotPerformAssertions
     */
    public function testSuccess(): void
    {
        $token = new Token(
            $value = Uuid::uuid4()->toString(),
            $expires = new DateTimeImmutable()
        );

        $token->validate($value, $expires->modify('-45 secs'));
    }

    public function testWarng(): void
    {
        $token = new Token(
            Uuid::uuid4()->toString(),
            $expires = new DateTimeImmutable()
        );

        $this->expectExceptionMessage('Token is not valid.');

        $token->validate(
            Uuid::uuid4()->toString(),
            $expires->modify('-45 secs')
        );
    }

    public function testExpired(): void
    {
        $token = new Token(
            $value = Uuid::uuid4()->toString(),
            $expires = new DateTimeImmutable()
        );

        $this->expectExceptionMessage('Token was expired.');
        $token->validate($value, $expires->modify('+45 secs'));
    }
}
