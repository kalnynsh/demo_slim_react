<?php

declare(strict_types=1);

namespace App\Http\Test\Unit;

use App\Http\HtmlResponse;
use Fig\Http\Message\StatusCodeInterface;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class HtmlResponseTest extends TestCase
{
    public function testDefault(): void
    {
        $response = new HtmlResponse($html = '<html lang="eng"></html>');

        self::assertEquals('text/html', $response->getHeaderLine('Content-Type'));
        self::assertEquals($html, $response->getBody()->getContents());
        self::assertEquals(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testWithCode(): void
    {
        $response = new HtmlResponse($html = '<html lang="eng"></html>', StatusCodeInterface::STATUS_CREATED);

        self::assertEquals('text/html', $response->getHeaderLine('Content-Type'));
        self::assertEquals($html, $response->getBody()->getContents());
        self::assertEquals(StatusCodeInterface::STATUS_CREATED, $response->getStatusCode());
    }
}
