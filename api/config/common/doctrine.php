<?php

declare(strict_types=1);

use App\Auth;
use Doctrine\ORM\ORMSetup;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\EventManager;
use Doctrine\Common\EventSubscriber;
use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Cache\Psr6\DoctrineProvider;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

return [
    EntityManagerInterface::class => function (ContainerInterface $container): EntityManagerInterface {
        /**
         * @psalm-suppress MixedAssignment
         * @psalm-var array{
         *   paths:array<string,string>,
         *   dev_mode:bool,
         *   proxy_dir:string,
         *   proxy_namespace:string,
         *   types:array<string,string>,
         *   connection:array<string,mixed>,
         *   cache_dir:string,
         *   subscribers:array<string>
         * } $settings
         */
        $settings = $container->get('config')['doctrine'];

        $config = Setup::createConfiguration(
            $settings['dev_mode'],
            $settings['proxy_dir'],
            $settings['cache_dir'] ?
                DoctrineProvider::wrap(new FilesystemAdapter('doctrine_queries', 0, $settings['cache_dir'])) :
                DoctrineProvider::wrap(new ArrayAdapter())
        );

        $metadataCache = $settings['dev_mode'] ?
            new ArrayAdapter() : new FilesystemAdapter('doctrine_metadata', 0, $settings['cache_dir']);

        /** @phan-suppress MixedArgument */
        $config->setMetadataCache($metadataCache);
        $driverImpl = ORMSetup::createDefaultAnnotationDriver($settings['paths'], $metadataCache);

        $config->setMetadataDriverImpl($driverImpl);
        $config->setProxyNamespace($settings['proxy_namespace']);
        $config->setNamingStrategy(new UnderscoreNamingStrategy());

        foreach ($settings['types'] as $name => $class) {
            if (! Type::hasType($name)) {
                /** @psalm-suppress ArgumentTypeCoercion */
                Type::addType($name, $class);
            }
        }

        if ($settings['dev_mode']) {
            $config->setAutoGenerateProxyClasses(true);
        }

        if (! $settings['dev_mode']) {
            $config->setAutoGenerateProxyClasses(false);
        }

        $eventManager = new EventManager();

        foreach ($settings['subscribers'] as $name) {
            /** @var EventSubscriber $subscriber */
            $subscriber = $container->get($name);
            $eventManager->addEventSubscriber($subscriber);
        }

        return EntityManager::create(
            $settings['connection'],
            $config,
            $eventManager
        );
    },
    Connection::class => static function (ContainerInterface $container): Connection {
        $em = $container->get(EntityManagerInterface::class);
        return $em->getConnection();
    },

    'config' => [
        'doctrine' => [
            'dev_mode' => false,
            'paths' => [
                __DIR__ . '/../../src/Auth/Entity',
            ],
            'proxy_dir' =>  __DIR__ . '/../../var/cache/doctrine/proxy',
            'cache_dir' =>  __DIR__ . '/../../var/cache/doctrine/cache',
            'proxy_namespace' => 'App\Proxies',
            'types' => [
                Auth\Entity\User\IdType::NAME => Auth\Entity\User\IdType::class,
                Auth\Entity\User\EmailType::NAME => Auth\Entity\User\EmailType::class,
                Auth\Entity\User\RoleType::NAME => Auth\Entity\User\RoleType::class,
                Auth\Entity\User\StatusType::NAME => Auth\Entity\User\StatusType::class,
            ],
            'connection' => [
                'driver' => 'pdo_pgsql',
                'host' => getenv('DB_HOST'),
                'user' => getenv('DB_USER'),
                'password' => getenv('DB_PASSWORD'),
                'dbname' => getenv('DB_NAME'),
                'charset' => 'UTF-8',
            ],
            'subscribers' => [],
        ],
    ],
];
