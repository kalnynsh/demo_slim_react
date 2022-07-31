<?php

declare(strict_types=1);

namespace Test\Functional\V1\Auth\Join;

use Fig\Http\Message\StatusCodeInterface;
use Test\Functional\Helper\Json;
use Test\Functional\WebTestCase;

/**
 * @covers \App\Http\Action\V1\Auth\Join\RequestAction
 *
 * @internal
 */
final class RequestTest extends WebTestCase
{
    private const URI = '/v1/auth/join';

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures([
            RequestFixture::class,
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
        $this->mailer()->clear();

        $email = 'violetta_' . self::getRandomNumber() . '-morgan@info.org';

        $response = $this->app()->handle(self::json('POST', self::URI, [
            'email' => $email,
            'password' => 'very-secret',
        ]));

        self::assertEquals(
            StatusCodeInterface::STATUS_CREATED,
            $response->getStatusCode()
        );

        self::assertEquals('{}', (string)$response->getBody());

        self::assertTrue($this->mailer()->hasEmailSentTo($email));
    }

    public function testExisting(): void
    {
        $response = $this->app()->handle(self::json('POST', self::URI, [
            'email' => RequestFixture::DEFAULT_USER_EMAIL,
            'password' => 'very-secret',
        ]));

        self::assertEquals(StatusCodeInterface::STATUS_CONFLICT, $response->getStatusCode());
        self::assertJson($body = (string)$response->getBody());

        self::assertEquals([
            'message' => 'User already exists.',
        ], Json::decode($body));
    }

    public function testExistingLanguage(): void
    {
        $response = $this->app()->handle(self::json('POST', self::URI, [
            'email' => RequestFixture::DEFAULT_USER_EMAIL,
            'password' => 'very-secret',
        ]))->withHeader('Accept-Language', 'ru');

        self::assertEquals(StatusCodeInterface::STATUS_CONFLICT, $response->getStatusCode());
        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'User already exists.',
        ], $data);
    }

    public function testEmpty(): void
    {
        $response = $this->app()->handle(self::json('POST', self::URI, []));

        self::assertEquals(StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());

        self::assertEquals([
            'errors' => [
                'email' => 'This value should not be blank.',
                'password' => 'This value is too short. It should have 6 characters or more.',
            ],
        ], Json::decode($body));
    }

    public function testNotValid(): void
    {
        $response = $this->app()->handle(self::json('POST', self::URI, [
            'email' => 'not-email',
            'password' => '',
        ]));

        self::assertEquals(StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY, $response->getStatusCode());

        self::assertJson($body = (string)$response->getBody());

        self::assertEquals([
            'errors' => [
                'email' => 'This value is not a valid email address.',
                'password' => 'This value is too short. It should have 6 characters or more.',
            ],
        ], Json::decode($body));
    }

    public function testNotValidLanguage(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/auth/join', [
            'email' => 'not-email',
            'password' => '',
        ])->withHeader('Accept-Language', 'en;q=0.9, ru-RU,ru;q=0.7, *;q=0.5'));

        self::assertEquals(StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        self::assertJson($body = (string)$response->getBody());

        $data = Json::decode($body);

        self::assertEquals([
            'errors' => [
                'email' => 'This value is not a valid email address.',
                'password' => 'This value is too short. It should have 6 characters or more.',
            ],
        ], $data);
    }

    private static function getRandomNumber(): int
    {
        return random_int(1, 10000);
    }
}
