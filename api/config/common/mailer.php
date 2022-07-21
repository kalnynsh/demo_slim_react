<?php

declare(strict_types=1);

use Symfony\Component\Mailer\Mailer;
use Psr\Container\ContainerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\MailerInterface;

return [
    MailerInterface::class => static function (ContainerInterface $container) {
        /**
         * @psalm-suppress MixedAssignment
         * @var array{
         *  host:string,
         *  port:int,
         *  user:string,
         *  password:string,
         *  encryption:string,
         *  from: string,
         *  verify_peer:int
         * } $config
         */
        $config = $container->get('config')['mailer'];

        /** @psalm-suppress MixedOperand */
        $dsn = 'smtp://'
            . $config['user']
            . ':'
            . $config['password']
            . '@'
            . $config['host']
            . ':'
            . $config['port']
            . '?'
            . 'verify_peer='
            . $config['verify_peer']
        ;

        $transport = Transport::fromDsn($dsn);

        return new Mailer($transport);
    },

    'config' => [
        'mailer' => [
            'user'     => getenv('MAILER_USER'),
            'password' => getenv('MAILER_PASSWORD'),
            'host'     => getenv('MAILER_HOST'),
            'port'     => getenv('MAILER_PORT'),
            'encryption' => getenv('MAILER_ENCRYPTION'),
            'from'       => getenv('MAILER_FROM_EMAIL'),
            'verify_peer' => 1,
        ],
    ],
];
