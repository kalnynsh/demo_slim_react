<?php

declare(strict_types=1);

use App\Flusher;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

return [
    Flusher::class => static function (ContainerInterface $container): Flusher {
        $em = $container->get(EntityManagerInterface::class);

        return new Flusher($em);
    },
];
