<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\Token;

use App\Auth\Entity\User\Token;
use DateInterval;
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

        /** @psalm-suppress PossiblyFalseArgument */
        $token->validate($value, $expires->sub(new DateInterval('PT45S')));
    }

    public function testWarng(): void
    {
        $token = new Token(
            Uuid::uuid4()->toString(),
            $expires = new DateTimeImmutable()
        );

        $this->expectExceptionMessage('Token is not valid.');

        /** @psalm-suppress PossiblyFalseArgument */
        $token->validate(
            Uuid::uuid4()->toString(),
            $expires->sub(new DateInterval('PT45S'))
        );
    }

    public function testExpired(): void
    {
        $token = new Token(
            $value = Uuid::uuid4()->toString(),
            $expires = new DateTimeImmutable()
        );

        $this->expectExceptionMessage('Token was expired.');

        $token->validate($value, $expires->add(new DateInterval('PT45S')));
    }
}
