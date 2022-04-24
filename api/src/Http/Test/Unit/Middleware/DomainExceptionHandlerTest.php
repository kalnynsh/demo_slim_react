<?php

declare(strict_types=1);

namespace App\Http\Test\Unit\Middleware;

use Psr\Log\LoggerInterface;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ResponseFactory;
use Fig\Http\Message\StatusCodeInterface;
use Slim\Psr7\Factory\ServerRequestFactory;
use Psr\Http\Server\RequestHandlerInterface;
use App\Http\Middleware\DomainExceptionHandler;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @covers DomainExceptionHandler
 */
class DomainExceptionHandlerTest extends TestCase
{
    public function testNormal(): void
    {
        $logger = $this->createMock(LoggerInterface::class);

        $logger
            ->expects($this->never())
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
            ->expects($this->once())
            ->method('warning');

        $translator = $this->createStub(TranslatorInterface::class);

        $translator
            ->expects($this->once())
            ->method('trans')
            ->with(
                $this->equalTo('Error has occurred.'),
                $this->equalTo([]),
                $this->equalTo('exceptions')
            )->willReturn('Ошибка.');

        $middleware = new DomainExceptionHandler($logger, $translator);

        $handler = $this->createStub(RequestHandlerInterface::class);

        $handler->method('handle')
            ->willThrowException(new \DomainException('Error has occurred.'));

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
