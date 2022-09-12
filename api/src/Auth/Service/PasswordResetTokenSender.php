<?php

declare(strict_types=1);

namespace App\Auth\Service;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email as MimeEmail;
use Twig\Environment;

final class PasswordResetTokenSender
{
    public const URI = 'password/confirm';
    public const SUBJECT = 'Your confirmation of resetting password';
    public const TEMPLATE_PATH = 'auth/password/confirm.html.twig';

    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly Environment $twig
    ) {
    }

    public function send(Email $email, Token $token): void
    {
        /** @var MimeEmail $mimeEmail */
        $mimeEmail = (new MimeEmail())
            ->to($email->getValue())
            ->subject(self::SUBJECT)
            ->priority(MimeEmail::PRIORITY_HIGH)
            ->html(
                $this->twig->render(
                    self::TEMPLATE_PATH,
                    [
                        'uri' => self::URI,
                        'token' => $token,
                    ]
                )
            );

        $this->mailer->send($mimeEmail);
    }
}
