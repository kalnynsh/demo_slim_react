<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Auth\Join;

use App\Validator\Validator;
use App\Http\Response\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use App\Http\Exception\BadRequestHttpException;
use App\Auth\Command\JoinByEmail\Request\Command;
use App\Auth\Command\JoinByEmail\Request\Handler;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;

final class RequestAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly DenormalizerInterface $denormalizer,
        private readonly Handler $handler,
        private readonly Validator $validator
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            /** @var Command $command */
            $command = $this->denormalizer->denormalize($request->getParsedBody(), Command::class);
        } catch (NotNormalizableValueException) {
            throw new BadRequestHttpException($request);
        }

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new EmptyResponse(StatusCodeInterface::STATUS_CREATED);
    }
}
