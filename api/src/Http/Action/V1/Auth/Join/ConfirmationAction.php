<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Auth\Join;

use App\Auth\Command\JoinByEmail\Confirm\Command;
use App\Auth\Command\JoinByEmail\Confirm\Handler;
use App\Http\EmptyResponse;
use App\Http\Validator\Validator;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ConfirmationAction implements RequestHandlerInterface
{
    private Handler $handler;
    private Validator $validator;

    public function __construct(Handler $handler, Validator $validator)
    {
        $this->handler = $handler;
        $this->validator =  $validator;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /**
         * @var array{token:?string} $parsedBody
         */
        $parsedBody = $request->getParsedBody();

        $command = new Command();
        $command->token = $parsedBody['token'] ?? '';

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new EmptyResponse(StatusCodeInterface::STATUS_OK);
    }
}
