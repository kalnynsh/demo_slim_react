<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\Token;

use App\Auth\Entity\User\Token;
use DateInterval;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @covers \App\Auth\Entity\User\Token::isExpiredTo
 *
 * @internal
 */
final class ExpiresTest extends TestCase
{
    public function testNotExpired(): void
    {
        $token = new Token(
            Uuid::uuid4()->toString(),
            $expires = new DateTimeImmutable()
        );

        self::assertTrue($token->isExpiredTo($expires->add(new DateInterval('PT45S'))));

        self::assertTrue($token->isExpiredTo($expires));

        /** @psalm-suppress PossiblyFalseArgument */
        self::assertFalse($token->isExpiredTo($expires->sub(new DateInterval('PT45S'))));
    }
}
