<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class InputTrimmerMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @psalm-suppress MixedArgumentTypeCoercion */
        $request = $request
            ->withParsedBody(self::filterStrings($request->getParsedBody()))
            ->withUploadedFiles(self::filterFiles($request->getUploadedFiles()));

        return $handler->handle($request);
    }

    private static function filterStrings(null|array|object $items): null|array|object
    {
        if (!\is_array($items)) {
            return $items;
        }

        $result = [];

        /**
         * @var string $key
         * @var object|string|null $value
         */
        foreach ($items as $key => $value) {
            if (\is_string($value)) {
                $result[$key] = trim($value);
            }

            if (!\is_string($value)) {
                $result[$key] = self::filterStrings($value);
            }
        }

        return $result;
    }

    /**
     * @param array<string, array<string, UploadedFileInterface>|UploadedFileInterface> $items
     */
    private static function filterFiles($items): array
    {
        $result = [];

        foreach ($items as $key => $item) {
            if ($item instanceof UploadedFileInterface) {
                if ($item->getError() !== UPLOAD_ERR_NO_FILE) {
                    $result[$key] = $item;
                }
            } else {
                /** @psalm-suppress MixedArgumentTypeCoercion */
                $result[$key] = self::filterFiles($item);
            }
        }

        return $result;
    }
}
