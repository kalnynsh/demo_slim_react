<?php

declare(strict_types=1);

namespace Test\Functional\V1\Auth;

use App\Auth\Entity\User\Role;
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
                        AuthHeader::for(UserFixture::USER_ADMIN_ID, UserFixture::USER_ADMIN_ROLE)
                    )
            );

        self::assertEquals(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        self::assertJson($body = (string)$response->getBody());

        self::assertEquals([
            'id' => UserFixture::USER_ADMIN_ID,
            'role' => UserFixture::USER_ADMIN_ROLE,
        ], Json::decode($body));
    }

    public function testUser(): void
    {
        $userId = UserFixture::USER_ID;
        $userRole = Role::USER;

        $response = $this
            ->app()
            ->handle(
                self::json('GET', '/v1/auth/user')
                    ->withHeader(
                        'Authorization',
                        AuthHeader::for($userId, $userRole)
                    )
            );

        self::assertEquals(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        self::assertJson($body = (string)$response->getBody());

        self::assertEquals([
            'id' => $userId,
            'role' => $userRole,
        ], Json::decode($body));
    }
}
