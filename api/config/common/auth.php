<?php

declare(strict_types=1);

use App\Auth\Entity\User\User;
use App\Auth\Entity\User\UserRepository;
use App\Auth\Service\JoinConfirmationSender;
use App\Auth\Service\NewEmailConfirmTokenSender;
use App\Auth\Service\PasswordResetTokenSender;
use App\Auth\Service\Tokenizer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Psr\Container\ContainerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;

return [
    UserRepository::class => static function (ContainerInterface $container): UserRepository {
        $em = $container->get(EntityManagerInterface::class);

        $repository = $em->getRepository(User::class);

        return new UserRepository($em, $repository);
    },

    JoinConfirmationSender::class => static function (ContainerInterface $container): JoinConfirmationSender {
        $mailer = $container->get(MailerInterface::class);

        $twig = $container->get(Environment::class);

        return new JoinConfirmationSender(
            $mailer,
            $twig
        );
    },

    NewEmailConfirmTokenSender::class => static function (ContainerInterface $container): NewEmailConfirmTokenSender {
        $mailer = $container->get(MailerInterface::class);

        $twig = $container->get(Environment::class);

        return new NewEmailConfirmTokenSender(
            $mailer,
            $twig
        );
    },

    PasswordResetTokenSender::class => static function (ContainerInterface $container): PasswordResetTokenSender {
        $mailer = $container->get(MailerInterface::class);

        $twig = $container->get(Environment::class);

        return new PasswordResetTokenSender(
            $mailer,
            $twig
        );
    },

    Tokenizer::class => static function (ContainerInterface $container): Tokenizer {
        /**
         * @psalm-suppress MixedArrayAccess
         * @var array{token_ttl:string} $config
         */
        $config = $container->get('config')['auth'];

        return new Tokenizer(new \DateInterval($config['token_ttl']));
    },

    'config' => [
        'auth' => [
            'token_ttl' => 'PT1H',
        ],
    ],
];
