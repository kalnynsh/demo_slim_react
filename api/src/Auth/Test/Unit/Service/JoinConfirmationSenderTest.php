<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Service;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use App\Auth\Service\JoinConfirmationSender;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email as MimeEmail;
use Twig\Environment;

/**
 * @covers \App\Auth\Service\JoinConfirmationSender
 */
class JoinConfirmationSenderTest extends TestCase
{
    public function testSuccess(): void
    {
        $from = 'tester@app.test';
        $to = new Email('user@app.test');
        $token = new Token(Uuid::uuid4()->toString(), new \DateTimeImmutable());

        $confirmUrl = 'http://test.org/' . JoinConfirmationSender::URI . '?token=' . $token->getValue();

        $twig = $this->createMock(Environment::class);

        /** \PHPUnit\Framework\MockObject\MockObject $twig */
        $twig
            ->expects(self::once())
            ->method('render')
            ->with(
                $this->equalTo(JoinConfirmationSender::TEMPLATE_PATH),
                $this->equalTo([
                    'uri' => JoinConfirmationSender::URI,
                    'token' => $token,
                ])
            )
            ->willReturn($body = '<a href="' . $confirmUrl . '">' . $confirmUrl . '</a>');

        $mailer = $this->createMock(Mailer::class);

        /** \PHPUnit\Framework\MockObject\MockObject $mailer */
        $mailer
            ->expects(self::once())
            ->method('send')
            ->willReturnCallback(static function (MimeEmail $mimeEmail) use ($from, $to, $body): int {
                self::assertEquals([new Address($from)], $mimeEmail->getFrom());
                self::assertEquals([new Address($to->getValue())], $mimeEmail->getTo());
                self::assertEquals(JoinConfirmationSender::SUBJECT, $mimeEmail->getSubject());
                self::assertEquals($body, $mimeEmail->getHtmlBody());

                return 1;
            });

        $sender = new JoinConfirmationSender(
            $mailer,
            $twig,
            $from
        );

        $sender->send($to, $token);
    }
}
