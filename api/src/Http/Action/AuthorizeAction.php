<?php

declare(strict_types=1);

namespace App\Http\Action;

use Twig\Environment;
use Psr\Log\LoggerInterface;
use App\Http\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use League\OAuth2\Server\AuthorizationServer;
use Psr\Http\Message\ResponseFactoryInterface;
use League\OAuth2\Server\Exception\OAuthServerException;


final class AuthorizeAction implements RequestHandlerInterface
{
    private const TEMPLATE_PATH = 'oauth/authorize.html.twig';

    private AuthorizationServer $server;
    private LoggerInterface $logger;
    private Environment $template;
    private ResponseFactoryInterface $response;

    public function __construct(
        AuthorizationServer $server,
        LoggerInterface $logger,
        Environment $template,
        ResponseFactoryInterface $response
    ) {
        $this->server = $server;
        $this->logger = $logger;
        $this->template = $template;
        $this->response = $response;
    }

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
            $this->logger->warning($exception->getMessage(), ['exception', $exception]);

            return (new OAuthServerException(
                'Server error.',
                0,
                'unknown_error',
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR)
            );
        }
    }
}
