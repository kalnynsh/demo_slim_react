<?php

declare(strict_types=1);

namespace Test\Functional\Helper;

final class JsonHelper
{
    /** @psalm-suppress MixedReturnStatement */
    public static function decode(string $data): array
    {
        return \json_decode($data, true, 512, JSON_THROW_ON_ERROR);
    }
}
