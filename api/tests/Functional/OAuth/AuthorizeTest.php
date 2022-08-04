<?php

declare(strict_types=1);

namespace Test\Functional\OAuth;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Fig\Http\Message\StatusCodeInterface;
use Test\Functional\Helper\Json;
use Test\Functional\WebTestCase;

/**
 * @internal
 */
final class AuthorizeTest extends WebTestCase
{
    use ArraySubsetAsserts;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures([
            AuthorizeFixture::class,
        ]);
    }

    public function testWithoutParams(): void
    {
        $response = $this->app()->handle(self::html('GET', '/authorize'));
        self::assertEquals(StatusCodeInterface::STATUS_BAD_REQUEST, $response->getStatusCode());
    }

    public function testPageWithoutChallenge(): void
    {
        $response = $this->app()->handle(self::html(
            'GET',
            '/authorize?'
            . http_build_query([
                'response_type' => 'code',
                'client_id' => 'frontend',
                'redirect_uri' => 'http://localhost:8080/oauth',
                'scope' => 'common',
                'state' => 'sTaTe',
            ])
        ));

        self::assertEquals(StatusCodeInterface::STATUS_BAD_REQUEST, $response->getStatusCode());
        self::assertJson($content = (string)$response->getBody());

        $data = Json::decode($content);

        self::assertArraySubset([
            'error' => 'invalid_request',
        ], $data);
    }

    public function testPageWithChallenge(): void
    {
        $response = $this->app()->handle(self::html(
            'GET',
            '/authorize?'
            . http_build_query([
                'response_type' => 'code',
                'client_id' => 'frontend',
                'redirect_uri' => 'http://localhost:8080/oauth',
                'code_challenge' => PKCE::challenge(PKCE::verifier()),
                'code_challenge_method' => 'S256',
                'scope' => 'common',
                'state' => 'sTaTe',
            ])
        ));

        self::assertEquals(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        self::assertNotEmpty($content = (string)$response->getBody());
        self::assertStringContainsString('<title>Authorization</title>', $content);
    }

    public function testInvalidClient(): void
    {
        $response = $this->app()->handle(self::html(
            'GET',
            '/authorize?'
            . http_build_query([
                'response_type' => 'code',
                'client_id' => 'invalid',
                'redirect_uri' => 'http://localhost:8080/oauth',
                'code_challenge' => PKCE::challenge(PKCE::verifier()),
                'code_challenge_method' => 'S256',
                'scope' => 'common',
                'state' => 'sTaTe',
            ])
        ));

        self::assertEquals(StatusCodeInterface::STATUS_UNAUTHORIZED, $response->getStatusCode());
        self::assertJson($content = (string)$response->getBody());

        $data = Json::decode($content);

        self::assertArraySubset([
            'error' => 'invalid_client',
        ], $data);
    }

    public function testAuthActiveUser(): void
    {
        $response = $this->app()->handle(self::html(
            'POST',
            '/authorize?'
            . http_build_query([
                'response_type' => 'code',
                'client_id' => 'frontend',
                'redirect_uri' => 'http://localhost:8080/oauth',
                'code_challenge' => PKCE::challenge(PKCE::verifier()),
                'code_challenge_method' => 'S256',
                'scope' => 'common',
                'state' => 'sTaTe',
            ]),
            [
                'email' => 'john-aCTivater@test.org',
                'password' => 'very-secret-295',
            ]
        ));

        self::assertEquals(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
        self::assertNotEmpty($location = $response->getHeaderLine('Location'));

        /** @var array{query:string} $url */
        $url = parse_url($location);

        self::assertNotEmpty($url['query']);

        /** @var array{code:string,state:string} $query */
        parse_str($url['query'], $query);

        self::assertArrayHasKey('code', $query);
        self::assertNotEmpty($query['code']);
        self::assertArrayHasKey('state', $query);
        self::assertEquals('sTaTe', $query['state']);
    }

    public function testAuthWaitedUser(): void
    {
        $response = $this->app()->handle(self::html(
            'POST',
            '/authorize?'
            . http_build_query([
                'response_type' => 'code',
                'client_id' => 'frontend',
                'redirect_uri' => 'http://localhost:8080/oauth',
                'code_challenge' => PKCE::challenge(PKCE::verifier()),
                'code_challenge_method' => 'S256',
                'scope' => 'common',
                'state' => 'sTaTe',
            ]),
            [
                'email' => 'john-waiter@test.org',
                'password' => 'very-secret-295',
            ]
        ));

        self::assertEquals(StatusCodeInterface::STATUS_CONFLICT, $response->getStatusCode());
        self::assertNotEmpty($content = (string)$response->getBody());
        self::assertStringContainsString('User is not confirmed.', $content);
    }

    public function testAuthInvalidUser(): void
    {
        $response = $this->app()->handle(self::html(
            'POST',
            '/authorize?'
            . http_build_query([
                'response_type' => 'code',
                'client_id' => 'frontend',
                'redirect_uri' => 'http://localhost:8080/oauth',
                'code_challenge' => PKCE::challenge(PKCE::verifier()),
                'code_challenge_method' => 'S256',
                'scope' => 'common',
                'state' => 'sTaTe',
            ]),
            [
                'email' => 'john-activater@test.org',
                'password' => 'worng_pswd_Xyz381',
            ]
        ));

        self::assertEquals(StatusCodeInterface::STATUS_BAD_REQUEST, $response->getStatusCode());
        self::assertNotEmpty($content = (string)$response->getBody());
        self::assertStringContainsString('Incorrect email or password.', $content);
    }

    public function testAuthInvalidUserLang(): void
    {
        self::markTestIncomplete();

        $response = $this->app()->handle(self::html(
            'POST',
            '/authorize?'
            . http_build_query([
                'response_type' => 'code',
                'client_id' => 'frontend',
                'redirect_uri' => 'http://localhost:8080/oauth',
                'code_challenge' => PKCE::challenge(PKCE::verifier()),
                'code_challenge_method' => 'S256',
                'scope' => 'common',
                'state' => 'sTaTe',
            ]),
            [
                'email' => 'john-activater@test.org',
                'password' => 'worng_pswd_Xyz492',
            ]
        ))->withHeader('Accept-Language', 'ru');

        self::assertEquals(StatusCodeInterface::STATUS_BAD_REQUEST, $response->getStatusCode());
        self::assertNotEmpty($content = (string)$response->getBody());
        self::assertStringContainsString('Неверный email или пароль.', $content);
    }
}
