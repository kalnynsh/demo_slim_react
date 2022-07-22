<?php

declare(strict_types=1);

namespace App\Http;

use Fig\Http\Message\StatusCodeInterface;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Response;

class EmptyResponse extends Response
{
    public function __construct(int $status = StatusCodeInterface::STATUS_NO_CONTENT)
    {
        $content = "{}";

        parent::__construct(
            $status,
            null,
            (new StreamFactory())->createStream($content)
        );
    }
}
