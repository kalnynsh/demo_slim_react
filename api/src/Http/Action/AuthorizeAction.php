<?php

declare(strict_types=1);

namespace App\Http\Action;

use App\Http\Response\HtmlResponse;
use App\Sentry\Sentry;
use Exception;
use Fig\Http\Message\StatusCodeInterface;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Twig\Environment;

final class AuthorizeAction implements RequestHandlerInterface
{
    private const TEMPLATE_PATH = 'oauth/authorize.html.twig';

    private AuthorizationServer $server;
    private LoggerInterface $logger;
    private Environment $template;
    private ResponseFactoryInterface $response;
    private Sentry $sentry;

    public function __construct(
        AuthorizationServer $server,
        LoggerInterface $logger,
        Environment $template,
        ResponseFactoryInterface $response,
        Sentry $sentry
    ) {
        $this->server = $server;
        $this->logger = $logger;
        $this->template = $template;
        $this->response = $response;
        $this->sentry = $sentry;
    }

    /**
     * @psalm-suppress ImplementedReturnTypeMismatch
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress InvalidReturnStatement
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $authRequest = $this->server->validateAuthorizationRequest($request);

            if ($request->getMethod() === 'POST') {
                return $this
                    ->server
                    ->completeAuthorizationRequest(
                        $authRequest,
                        $this->response->createResponse()
                    );
            }

            return new HtmlResponse(
                $this->template->render(self::TEMPLATE_PATH)
            );
        } catch (OAuthServerException $exception) {
            $this->logger->warning($exception->getMessage(), ['exception', $exception]);

            return $exception->generateHttpResponse($this->response->createResponse());
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), ['exception', $exception]);
            $this->sentry->capture($exception);

            return new OAuthServerException(
                'Server error.',
                0,
                'unknown_error',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR
            );
        }
    }
}
