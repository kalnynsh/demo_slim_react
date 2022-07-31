<?php

declare(strict_types=1);

namespace Test\Functional\OAuth;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
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
        self::markTestIncomplete();

        $response = $this->app()->handle(self::html('GET', '/authorize'));
        self::assertEquals(400, $response->getStatusCode());
    }

    public function testPageWithoutChallenge(): void
    {
        self::markTestIncomplete();

        $response = $this->app()->handle(self::html(
            'GET',
            '/authorize?'
            . http_build_query([
                'response_type' => 'code',
                'client_id' => 'frontend',
                'redirect_url' => 'http://localhost:8080/oauth',
                'scope' => 'common',
                'state' => 'State',
            ])
        ));

        self::assertEquals(401, $response->getStatusCode());
    }

    public function testPageWithChallenge(): void
    {
        self::markTestIncomplete();

        $response = $this->app()->handle(self::html(
            'GET',
            '/authorize?'
            . http_build_query([
                'response_type' => 'code',
                'client_id' => 'frontend',
                'redirect_url' => 'http://localhost:8080/oauth',
                'code_challenge' => PKCE::challenge(PKCE::verifier()),
                'code_challenge_method' => 'S256',
                'scope' => 'common',
                'state' => 'State',
            ])
        ));

        self::assertEquals(200, $response->getStatusCode());
        self::assertNotEmpty($content = (string)$response->getBody());
        self::assertStringContainsString('<title>Auth</title>', $content);
    }

    public function testInvalidClient(): void
    {
        self::markTestIncomplete();

        $response = $this->app()->handle(self::html(
            'GET',
            '/authorize?'
            . http_build_query([
                'response_type' => 'code',
                'client_id' => 'invalid',
                'redirect_url' => 'http://localhost:8080/oauth',
                'code_challenge' => PKCE::challenge(PKCE::verifier()),
                'code_challenge_method' => 'S256',
                'scope' => 'common',
                'state' => 'State',
            ])
        ));

        self::assertEquals(401, $response->getStatusCode());
        self::assertJson($content = (string)$response->getBody());

        $data = Json::decode($content);

        self::assertArraySubset([
            'error' => 'invalid_client',
        ], $data);
    }

    public function testAuthActiveUser(): void
    {
        self::markTestIncomplete();

        $response = $this->app()->handle(self::html(
            'POST',
            '/authorize?'
            . http_build_query([
                'response_type' => 'code',
                'client_id' => 'frontend',
                'redirect_url' => 'http://localhost:8080/oauth',
                'code_challenge' => PKCE::challenge(PKCE::verifier()),
                'code_challenge_method' => 'S256',
                'scope' => 'common',
                'state' => 'State',
            ]),
            [
                'email' => 'john-activater@test.org',
                'password' => 'very-secret-295',
            ]
        ));

        self::assertEquals(302, $response->getStatusCode());
        self::assertNotEmpty($location = $response->getHeaderLine('Location'));

        /** @var array{query:string} $url */
        $url = parse_url($location);

        self::assertNotEmpty($url['query']);

        /** @var array{code:string,state:string} $query */
        parse_str($url['query'], $query);

        self::assertArrayHasKey('code', $query);
        self::assertNotEmpty($query['code']);
        self::assertArrayHasKey('state', $query);
        self::assertEquals('State', $query['state']);
    }

    public function testAuthWaitedUser(): void
    {
        self::markTestIncomplete();

        $response = $this->app()->handle(self::html(
            'POST',
            '/authorize?'
            . http_build_query([
                'response_type' => 'code',
                'client_id' => 'frontend',
                'redirect_url' => 'http://localhost:8080/oauth',
                'code_challenge' => PKCE::challenge(PKCE::verifier()),
                'code_challenge_method' => 'S256',
                'scope' => 'common',
                'state' => 'State',
            ]),
            [
                'email' => 'john-waiter@test.org',
                'password' => 'very-secret-295',
            ]
        ));

        self::assertEquals(409, $response->getStatusCode());
        self::assertNotEmpty($content = (string)$response->getBody());
        self::assertStringContainsString('User is not confirmed.', $content);
    }

    public function testAuthInvalidUser(): void
    {
        self::markTestIncomplete();

        $response = $this->app()->handle(self::html(
            'POST',
            '/authorize?'
            . http_build_query([
                'response_type' => 'code',
                'client_id' => 'frontend',
                'redirect_url' => 'http://localhost:8080/oauth',
                'code_challenge' => PKCE::challenge(PKCE::verifier()),
                'code_challenge_method' => 'S256',
                'scope' => 'common',
                'state' => 'State',
            ]),
            [
                'email' => 'john-activater@test.org',
                'password' => 'worng_pswd_Xyz381',
            ]
        ));

        self::assertEquals(400, $response->getStatusCode());
        self::assertNotEmpty($content = (string)$response->getBody());
        self::assertStringContainsString('Incorrect email or password.', $content);
    }
}
