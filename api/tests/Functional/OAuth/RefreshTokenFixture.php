<?php

declare(strict_types=1);

namespace Test\Functional\OAuth;

use App\Auth\Entity\User\Id;
use App\Auth\Test\Builder\UserBuilder;
use App\OAuth\Entity\Client;
use App\OAuth\Entity\RefreshToken;
use App\OAuth\Test\Builder\AccessTokenBuilder;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class RefreshTokenFixture extends AbstractFixture
{
    public const USER_ID = '00000000-0000-0000-0000-000000000003';
    public const REFRESH_TOKEN_ID = 'gxd54260k274tepcb214cj4139b9e';

    public function load(ObjectManager $manager): void
    {
        $user = (new UserBuilder())
            ->withId(new Id(self::USER_ID))
            ->active()
            ->build();

        $manager->persist($user);

        $client = new Client(
            identifier: 'frontend',
            name: 'Frontend',
            redirectUri: 'http://localhost/oauth'
        );

        $accessToken = (new AccessTokenBuilder())
            ->withUserIdentifier(self::USER_ID)
            ->build($client);

        $refreshToken = new RefreshToken();
        $refreshToken->setAccessToken($accessToken);
        $refreshToken->setExpiryDateTime(new DateTimeImmutable('2300-12-31 21:00:01'));
        $refreshToken->setIdentifier(self::REFRESH_TOKEN_ID);

        $manager->persist($refreshToken);

        $manager->flush();
    }
}
