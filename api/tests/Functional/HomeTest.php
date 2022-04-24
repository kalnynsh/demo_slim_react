<?php

declare(strict_types=1);

namespace Test\Functional;

/**
 * @coversNothing
 */
class HomeTest extends WebTestCase
{
    public function testMethod(): void
    {
        $response = $this->app()->handle(self::json('POST', '/'));

        // 405 - Method Not Allowed
        self::assertEquals(405, $response->getStatusCode());
    }

    public function testSuccess(): void
    {
        $response = $this->app()->handle(self::json('GET', '/'));

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        self::assertEquals('{"title":"Hello!"}', $response->getBody()->getContents());
    }
}
