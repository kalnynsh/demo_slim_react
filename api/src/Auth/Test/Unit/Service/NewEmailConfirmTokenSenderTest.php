<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Service;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use App\Auth\Service\NewEmailConfirmTokenSender;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email as MimeEmail;
use Twig\Environment;

/**
 * @covers \App\Auth\Service\NewEmailConfirmTokenSender
 */
class NewEmailConfirmTokenSenderTest extends TestCase
{
    public function testSuccess(): void
    {
        $from = 'tester@app.test';
        $to = new Email('user@app.test');
        $token = new Token(Uuid::uuid4()->toString(), new \DateTimeImmutable());

        $confirmUrl = 'http://test.org/' . NewEmailConfirmTokenSender::URI . '?token=' . $token->getValue();

        $twig = $this->createMock(Environment::class);

        /** \PHPUnit\Framework\MockObject\MockObject $twig */
        $twig
            ->expects(self::once())
            ->method('render')
            ->with(
                $this->equalTo(NewEmailConfirmTokenSender::TEMPLATE_PATH),
                $this->equalTo([
                    'uri' => NewEmailConfirmTokenSender::URI,
                    'token' => $token,
                ])
            )
            ->willReturn($body = '<a href="' . $confirmUrl . '">' . $confirmUrl . '</a>')
        ;

        $mailer = $this->createMock(Mailer::class);

        /** \PHPUnit\Framework\MockObject\MockObject $mailer */
        $mailer
            ->expects(self::once())
            ->method('send')
            ->willReturnCallback(static function (MimeEmail $mimeEmail) use ($from, $to, $body): int {
                self::assertEquals([new Address($from)], $mimeEmail->getFrom());
                self::assertEquals([new Address($to->getValue())], $mimeEmail->getTo());
                self::assertEquals(NewEmailConfirmTokenSender::SUBJECT, $mimeEmail->getSubject());
                self::assertEquals($body, $mimeEmail->getHtmlBody());

                return 1;
            })
        ;

        $sender = new NewEmailConfirmTokenSender(
            $mailer,
            $twig,
            $from
        );

        $sender->send($to, $token);
    }
}
