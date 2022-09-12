<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Auth\Join;

use App\Auth\Command\JoinByEmail\Request\Command;
use App\Auth\Command\JoinByEmail\Request\Handler;
use App\Http\Response\EmptyResponse;
use App\Validator\Validator;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RequestAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly Handler $handler,
        private readonly Validator $validator
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var array{email:?string, password:?string} */
        $body = $request->getParsedBody();

        $command = new Command();
        $command->email = $body['email'] ?? '';
        $command->password = $body['password'] ?? '';

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new EmptyResponse(StatusCodeInterface::STATUS_CREATED);
    }
}
