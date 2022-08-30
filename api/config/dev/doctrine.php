<?php

declare(strict_types=1);

use App\Data\Doctrine\FixDefaultSchemaSubscriber;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

return [
    ORMPurger::class => static function (ContainerInterface $container) {
        $em = $container->get(EntityManagerInterface::class);

        return new ORMPurger($em);
    },

    ORMExecutor::class => static function (ContainerInterface $container) {
        $em = $container->get(EntityManagerInterface::class);

        $ormPurger = $container->get(ORMPurger::class);

        return new ORMExecutor($em, $ormPurger);
    },

    'config' => [
        'doctrine' => [
            'dev_mode' => true,
            'cache_dir' => null,
            'proxy_dir' => __DIR__ . '/../../var/cache/' . PHP_SAPI . '/doctrine/proxy',
            'subscribers' => [
                FixDefaultSchemaSubscriber::class,
            ],
        ],
    ],
];
