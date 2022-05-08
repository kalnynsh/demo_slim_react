<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class InputTrimmerMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $request = $request
            ->withParsedBody(self::filterStrings($request->getParsedBody()))
            ->withUploadedFiles(self::filterFiles($request->getUploadedFiles()));

            return $handler->handle($request);
    }

    /**
     * @param null|array|object $items
     * @return null|array|object
     */
    private static function filterStrings($items)
    {
        if (! \is_array($items)) {
            return $items;
        }

        $result = [];

        /**
         * @var string $key
         * @var null|string|object $value
         */
        foreach ($items as $key => $value) {
            if (\is_string($value)) {
                $result[$key] = \trim($value);
            }

            if (! \is_string($value)) {
                $result[$key] = self::filterStrings($value);
            }
        }

        return $result;
    }

    /**
     *
     * @param array<string, array<string, UploadedFileInterface>|UploadedFileInterface> $items
     * @return array
     */
    private static function filterFiles($items): array
    {
        $result = [];

        /**
         * @var string $key
         * @var array|UploadedFileInterface $item
         */
        foreach ($items as $key => $item) {
            if ($item instanceof UploadedFileInterface) {
                if ($item->getError() !== UPLOAD_ERR_NO_FILE) {
                    $result[$key] = $item;
                }
            } else {
                $result[$key] = self::filterFiles($item);
            }
        }

        return $result;
    }
}
