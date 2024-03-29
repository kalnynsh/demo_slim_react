<?php

declare(strict_types=1);

namespace Test\Functional\OAuth;

final class PKCE
{
    public static function verifier(): string
    {
        $bytes = random_bytes(64);

        return strtr(rtrim(base64_encode($bytes), '='), '+/', '-_');
    }

    public static function challenge(string $verifier): string
    {
        $challengeBytes = hash('sha256', $verifier, true);

        return strtr(rtrim(base64_encode($challengeBytes), '='), '+/', '-_');
    }
}
