<?php

declare(strict_types=1);

namespace App\OAuth\Entity;

use App\Auth\Query\FindIdentityById\Fetcher;
use Fig\Http\Message\StatusCodeInterface;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;

final class AccessTokenRepository implements AccessTokenRepositoryInterface
{
    private Fetcher $users;

    public function __construct(Fetcher $users)
    {
        $this->users = $users;
    }

    public function getNewToken(
        ClientEntityInterface $clientEntity,
        array $scopes,
        $userIdentifier = null
    ): AccessToken {
        $accessToken = new AccessToken($clientEntity, $scopes);

        if ($userIdentifier !== null) {
            $identity = $this->users->fetch((string)$userIdentifier);

            if ($identity === null) {
                throw new OAuthServerException(
                    'User is not found.',
                    101,
                    'invalid_user',
                    StatusCodeInterface::STATUS_UNAUTHORIZED
                );
            }

            $accessToken->setUserIdentifier($identity->id);
            $accessToken->setUserRole($identity->role);
        }

        return $accessToken;
    }

    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
    {
        // Do nothing
    }

    public function revokeAccessToken($tokenId): void
    {
        // Do nothing
    }

    public function isAccessTokenRevoked($tokenId): bool
    {
        return false;
    }
}
