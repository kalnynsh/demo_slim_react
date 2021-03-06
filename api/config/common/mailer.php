<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;

use function App\env;

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
            . $config['verify_peer'];

        $transport = Transport::fromDsn($dsn);

        return new Mailer($transport);
    },

    'config' => [
        'mailer' => [
            'user'     => env('MAILER_USER'),
            'password' => env('MAILER_PASSWORD'),
            'host'     => env('MAILER_HOST'),
            'port'     => env('MAILER_PORT'),
            'encryption' => env('MAILER_ENCRYPTION'),
            'from'       => env('MAILER_FROM_EMAIL'),
            'verify_peer' => 1,
        ],
    ],
];
