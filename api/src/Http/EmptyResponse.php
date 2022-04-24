<?php

declare(strict_types=1);

namespace App\Http;

use Slim\Psr7\Response;
use Fig\Http\Message\StatusCodeInterface;
use Http\Factory\Guzzle\StreamFactory;

class EmptyResponse extends Response
{
    public function __construct(int $status = StatusCodeInterface::STATUS_NO_CONTENT)
    {
        parent::__construct(
            $status,
            null,
            (new StreamFactory())->createStreamFromResource(\fopen('php://temp', 'rb'))
        );
    }
}
