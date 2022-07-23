<?php

declare(strict_types=1);

namespace App\Http\Test\Unit;

use App\Http\EmptyResponse;
use Fig\Http\Message\StatusCodeInterface;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class EmptyResponseTest extends TestCase
{
    public function testDefault(): void
    {
        $response = new EmptyResponse();

        self::assertEquals(StatusCodeInterface::STATUS_NO_CONTENT, $response->getStatusCode());
        self::assertFalse($response->hasHeader('Content-Type'));

        self::assertEquals('{}', (string)$response->getBody());
        self::assertTrue($response->getBody()->isWritable());
    }

    public function testWithCode(): void
    {
        $response = new EmptyResponse(StatusCodeInterface::STATUS_CREATED);

        self::assertEquals(StatusCodeInterface::STATUS_CREATED, $response->getStatusCode());
    }
}
