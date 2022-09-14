<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Auth;

use App\Http\Exception\UnauthorizedHttpException;
use App\Http\Middleware\Auth\Authenticate;
use App\Http\Middleware\Auth\Identity;
use App\Http\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class UserAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly SerializerInterface $serializer
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var Identity $identity */
        $identity = Authenticate::identity($request);

        /** @psalm-suppress DocblockTypeContradiction */
        if ($identity === null) {
            throw new UnauthorizedHttpException($request);
        }

        return new JsonResponse($this->serialize->serialize($identity, 'json'), 200, true);
    }
}
