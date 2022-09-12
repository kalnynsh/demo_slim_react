<?php

declare(strict_types=1);

namespace App\Http\Action;

use App\Auth\Query\FindIdByCredentials\Fetcher;
use App\Auth\Query\FindIdByCredentials\Query;
use App\Http\Response\HtmlResponse;
use App\OAuth\Entity\User;
use Exception;
use Fig\Http\Message\StatusCodeInterface;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class AuthorizeAction implements RequestHandlerInterface
{
    private const TEMPLATE_PATH = 'oauth/authorize.html.twig';

    public function __construct(
        private readonly AuthorizationServer $server,
        private readonly LoggerInterface $logger,
        private readonly Fetcher $users,
        private readonly Environment $template,
        private readonly ResponseFactoryInterface $response,
        private readonly TranslatorInterface $translator
    ) {
    }

    /**
     * @psalm-suppress ImplementedReturnTypeMismatch
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress InvalidReturnStatement
     *
     * @throws OAuthServerException
     * @throws Exception
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $authRequest = $this->server->validateAuthorizationRequest($request);

            $query = new Query();

            if ($request->getMethod() === 'POST') {
                /**
                 * @var array{
                 *   email?: ?string,
                 *   password?: ?string
                 * } $body
                 */
                $body = $request->getParsedBody();

                $query->email = $body['email'] ?? '';
                $query->password = $body['password'] ?? '';

                $user = $this->users->fetch($query);

                if ($user === null) {
                    $error = $this->translator->trans('Incorrect email or password.', [], 'oauth');

                    return new HtmlResponse(
                        $this->template->render(self::TEMPLATE_PATH, compact('query', 'error')),
                        StatusCodeInterface::STATUS_BAD_REQUEST
                    );
                }

                if (!$user->isActive) {
                    $error = $this->translator->trans('User is not confirmed.', [], 'oauth');

                    return new HtmlResponse(
                        $this->template->render(self::TEMPLATE_PATH, compact('query', 'error')),
                        StatusCodeInterface::STATUS_CONFLICT
                    );
                }

                $authRequest->setUser(new User($user->id));
                $authRequest->setAuthorizationApproved(true);

                return $this
                    ->server
                    ->completeAuthorizationRequest(
                        $authRequest,
                        $this->response->createResponse()
                    );
            }

            return new HtmlResponse(
                $this->template->render(self::TEMPLATE_PATH, compact('query'))
            );
        } catch (OAuthServerException $exception) {
            $this->logger->warning($exception->getMessage(), ['exception', $exception]);

            return $exception->generateHttpResponse($this->response->createResponse());
        }
    }
}
