<?php

declare(strict_types=1);

namespace App\Http\Test\Unit\Middleware;

use App\Http\Middleware\DomainExceptionHandler;
use DomainException;
use Fig\Http\Message\StatusCodeInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @covers \App\Http\Middleware\DomainExceptionHandler
 *
 * @internal
 */
final class DomainExceptionHandlerTest extends TestCase
{
    public function testNormal(): void
    {
        $logger = $this->createMock(LoggerInterface::class);

        $logger
            ->expects(self::never())
            ->method('warning');

        $translator = $this->createStub(TranslatorInterface::class);

        $middleware = new DomainExceptionHandler($logger, $translator);

        $handler = $this->createStub(RequestHandlerInterface::class);

        $source = (new ResponseFactory())->createResponse();

        $handler->method('handle')->willReturn($source);

        $request = (new ServerRequestFactory())->createServerRequest('POST', 'http://test.org');
        $response = $middleware->process($request, $handler);

        self::assertEquals($source, $response);
    }

    public function testException(): void
    {
        $logger = $this->createMock(LoggerInterface::class);

        $logger
            ->expects(self::once())
            ->method('warning');

        /** @psalm-suppress UndefinedInterfaceMethod */
        $translator = $this->createStub(TranslatorInterface::class);

        /**
         * @psalm-suppress MixedMethodCall
         * @psalm-suppress UndefinedInterfaceMethod
         */
        $translator
            ->expects(self::once())
            ->method('trans')
            ->with(
                self::equalTo('Error has occurred.'),
                self::equalTo([]),
                self::equalTo('exceptions')
            )->willReturn('Ошибка.');

        $middleware = new DomainExceptionHandler($logger, $translator);

        $handler = $this->createStub(RequestHandlerInterface::class);

        $handler->method('handle')
            ->willThrowException(new DomainException('Error has occurred.'));

        $request = (new ServerRequestFactory())->createServerRequest('POST', 'http://test.org');
        $response = $middleware->process($request, $handler);

        self::assertEquals(StatusCodeInterface::STATUS_CONFLICT, $response->getStatusCode());
        self::assertJson($body = (string) $response->getBody());

        /** @var array $bodyDecoded */
        $bodyDecoded = \json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals([
            'message' => 'Ошибка.',
        ], $bodyDecoded);
    }
}
