<?php

declare(strict_types=1);

namespace Test\Functional\V1\Auth;

use Fig\Http\Message\StatusCodeInterface;
use Test\Functional\Helper\Json;
use Test\Functional\OAuth\AuthHeader;
use Test\Functional\WebTestCase;

/**
 * @internal
 */
final class UserTest extends WebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures([
            'user' => UserFixture::class,
        ]);
    }

    public function testGuest(): void
    {
        $response = $this
            ->app()
            ->handle(self::json('GET', '/v1/auth/user'));

        self::assertEquals(StatusCodeInterface::STATUS_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testAdmin(): void
    {
        $response = $this
            ->app()
            ->handle(
                self::json('GET', '/v1/auth/user')
                    ->withHeader(
                        'Authorization',
                        AuthHeader::for(UserFixture::USER_ADMIN_ID, UserFixture::ROLE_ADMIN)
                    )
            );

        self::assertEquals(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        self::assertJson($body = (string)$response->getBody());

        self::assertEquals([
            'id' => UserFixture::USER_ADMIN_ID,
            'role' => UserFixture::ROLE_ADMIN,
        ], Json::decode($body));
    }

    public function testUser(): void
    {
        $response = $this
            ->app()
            ->handle(
                self::json('GET', '/v1/auth/user')
                    ->withHeader(
                        'Authorization',
                        AuthHeader::for(UserFixture::USER_ID, UserFixture::ROLE_USER)
                    )
            );

        self::assertEquals(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        self::assertJson($body = (string)$response->getBody());

        self::assertEquals([
            'id' => UserFixture::USER_ID,
            'role' => UserFixture::ROLE_USER,
        ], Json::decode($body));
    }
}
