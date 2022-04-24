<?php

declare(strict_types=1);

namespace Test\Functional\V1\Auth\Join;

use Test\Functional\WebTestCase;
use Fig\Http\Message\StatusCodeInterface;
use Ramsey\Uuid\Uuid;
use Test\Functional\Helper\JsonHelper;

class ConfirmationTest extends WebTestCase
{
    private const URI = '/v1/auth/join/confirm';

    public function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures([
            ConfirmationFixture::class,
        ]);
    }

    public function testMethod(): void
    {
        $response = $this->app()->handle(self::json('GET', self::URI));

        self::assertEquals(
            StatusCodeInterface::STATUS_METHOD_NOT_ALLOWED,
            $response->getStatusCode()
        );
    }

    public function testSuccess(): void
    {
        $response = $this->app()->handle(self::json('POST', self::URI, [
            'token' => ConfirmationFixture::VALID,
        ]));

        self::assertEquals(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        self::assertEquals('', (string) $response->getBody());
    }

    public function testExpired(): void
    {
        $response = $this->app()->handle(self::json('POST', self::URI, [
            'token' => ConfirmationFixture::EXPIRED,
        ]));

        self::assertEquals(StatusCodeInterface::STATUS_CONFLICT, $response->getStatusCode());
        self::assertJson($body = (string) $response->getBody());

        self::assertEquals([
            'message' => 'Token was expired.',
        ], JsonHelper::decode($body));
    }

    public function testEmpty(): void
    {
        $response = $this->app()->handle(self::json('POST', self::URI, []));

        self::assertEquals(StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        self::assertJson($body = (string) $response->getBody());

        self::assertEquals([
            'errors' => [
                'token' => 'This value should not be blank.',
            ],
        ], JsonHelper::decode($body));
    }

    public function testNotExistes(): void
    {
        $response = $this->app()->handle(self::json('POST', self::URI, [
            'token' => Uuid::uuid4()->toString(),
        ]));

        self::assertEquals(StatusCodeInterface::STATUS_CONFLICT, $response->getStatusCode());
        self::assertJson($body = (string) $response->getBody());

        self::assertEquals([
            'message' => 'Incorrect token.',
        ], JsonHelper::decode($body));
    }
}
