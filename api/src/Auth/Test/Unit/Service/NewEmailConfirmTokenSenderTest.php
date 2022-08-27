<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Service;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use App\Auth\Service\NewEmailConfirmTokenSender;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email as MimeEmail;
use Twig\Environment;

/**
 * @covers \App\Auth\Service\NewEmailConfirmTokenSender
 *
 * @internal
 */
final class NewEmailConfirmTokenSenderTest extends TestCase
{
    public function testSuccess(): void
    {
        $to = new Email('user@app.test');
        $token = new Token(Uuid::uuid4()->toString(), new DateTimeImmutable());

        $confirmUrl = 'http://test.org/' . NewEmailConfirmTokenSender::URI . '?token=' . $token->getValue();

        $twig = $this->createMock(Environment::class);

        /** \PHPUnit\Framework\MockObject\MockObject $twig */
        $twig
            ->expects(self::once())
            ->method('render')
            ->with(
                self::equalTo(NewEmailConfirmTokenSender::TEMPLATE_PATH),
                self::equalTo([
                    'uri' => NewEmailConfirmTokenSender::URI,
                    'token' => $token,
                ])
            )
            ->willReturn($body = '<a href="' . $confirmUrl . '">' . $confirmUrl . '</a>');

        $mailer = $this->createMock(Mailer::class);

        /** \PHPUnit\Framework\MockObject\MockObject $mailer */
        $mailer
            ->expects(self::once())
            ->method('send')
            ->willReturnCallback(static function (MimeEmail $mimeEmail) use ($to, $body): void {
                self::assertEquals($to->getValue(), $mimeEmail->getTo()[0]->getAddress());
                self::assertEquals(NewEmailConfirmTokenSender::SUBJECT, $mimeEmail->getSubject());
                self::assertEquals($body, $mimeEmail->getHtmlBody());
            });

        $sender = new NewEmailConfirmTokenSender($mailer, $twig);

        $sender->send($to, $token);
    }
}
