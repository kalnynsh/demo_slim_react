<?php

declare(strict_types=1);

namespace App\Http\Test\Unit\Middleware;

use App\Http\Middleware\InputTrimmerMiddleware as MiddlewareInputTrimmerMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Factory\UploadedFileFactory;

/**
 * @covers \App\Http\Middleware\InputTrimmerMiddleware
 *
 * @internal
 */
final class InputTrimmerMiddlewareTest extends TestCase
{
    public function testParsedBody(): void
    {
        $middleware = new MiddlewareInputTrimmerMiddleware();

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', 'http://test.org')
            ->withParsedBody([
                'null' => null,
                'space' => ' ',
                'string' => ' String ',
                'int' => 42,
                'nested' => [
                    'null' => null,
                    'space' => ' ',
                    'name' => ' Name',
                ],
            ]);

        $handler = $this->createMock(RequestHandlerInterface::class);

        $handler
            ->expects(self::once())
            ->method('handle')
            ->willReturnCallback(static function (ServerRequestInterface $request): ResponseInterface {
                self::assertEquals([
                    'null' => null,
                    'space' => '',
                    'string' => 'String',
                    'int' => 42,
                    'nested' => [
                        'null' => null,
                        'space' => '',
                        'name' => 'Name',
                    ],
                ], $request->getParsedBody());

                return (new ResponseFactory())->createResponse();
            });

        $middleware->process($request, $handler);
    }

    public function testUploadedFiles(): void
    {
        $middleware = new MiddlewareInputTrimmerMiddleware();

        $realFile = (new UploadedFileFactory())
            ->createUploadedFile(
                (new StreamFactory())->createStream(''),
                0,
                UPLOAD_ERR_OK
            );

        $noFile = (new UploadedFileFactory())
            ->createUploadedFile(
                (new StreamFactory())->createStream(''),
                0,
                UPLOAD_ERR_NO_FILE
            );

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', 'http://test.org')
            ->withUploadedFiles([
                'real_file' => $realFile,
                'none_file' => $noFile,
                'files' => [$realFile, $noFile],
            ]);

        $handler = $this->createMock(RequestHandlerInterface::class);

        $handler
            ->expects(self::once())
            ->method('handle')
            ->willReturnCallback(static function (ServerRequestInterface $request) use ($realFile): ResponseInterface {
                self::assertEquals([
                    'real_file' => $realFile,
                    'files' => [$realFile],
                ], $request->getUploadedFiles());

                return (new ResponseFactory())->createResponse();
            });

        $middleware->process($request, $handler);
    }
}
