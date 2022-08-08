<?php

declare(strict_types=1);

use Doctrine\Common\EventManager;
use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\ORMSetup;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

use function App\env;

return [
    EntityManagerInterface::class => static function (ContainerInterface $container): EntityManager {
        /**
         * @psalm-suppress MixedAssignment
         * @var array{
         *   paths:array<string,string>,
         *   dev_mode:bool,
         *   proxy_dir:string,
         *   proxy_namespace:string,
         *   types:array<string,class-string<Doctrine\DBAL\Types\Type>>,
         *   connection:array<string,mixed>,
         *   cache_dir:string,
         *   subscribers:array<string>
         * } $settings
         */
        $settings = $container->get('config')['doctrine'];

        $config = ORMSetup::createAnnotationMetadataConfiguration(
            $settings['paths'],
            $settings['dev_mode'],
            $settings['proxy_dir'],
            $settings['cache_dir'] ?
                new FilesystemAdapter('doctrine_queries', 0, $settings['cache_dir']) :
                new ArrayAdapter()
        );

        $config->setProxyNamespace($settings['proxy_namespace']);
        $config->setNamingStrategy(new UnderscoreNamingStrategy());

        foreach ($settings['types'] as $name => $class) {
            if (!Type::hasType($name)) {
                /** @psalm-suppress ArgumentTypeCoercion */
                Type::addType($name, $class);
            }
        }

        if ($settings['dev_mode']) {
            $config->setAutoGenerateProxyClasses(true);
        }

        if (!$settings['dev_mode']) {
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
                __DIR__ . '/../../src/OAuth/Entity',
            ],
            'proxy_dir' =>  __DIR__ . '/../../var/cache/doctrine/proxy',
            'cache_dir' =>  __DIR__ . '/../../var/cache/doctrine/cache',
            'proxy_namespace' => 'App\Proxies',
            'types' => [
                App\Auth\Entity\User\IdType::NAME => App\Auth\Entity\User\IdType::class,
                App\Auth\Entity\User\EmailType::NAME => App\Auth\Entity\User\EmailType::class,
                App\Auth\Entity\User\RoleType::NAME => App\Auth\Entity\User\RoleType::class,
                App\Auth\Entity\User\StatusType::NAME => App\Auth\Entity\User\StatusType::class,
            ],
            'connection' => [
                'driver' => 'pdo_pgsql',
                'host' => env('DB_HOST'),
                'user' => env('DB_USER'),
                'password' => env('DB_PASSWORD'),
                'dbname' => env('DB_NAME'),
                'charset' => 'UTF-8',
            ],
            'subscribers' => [],
        ],
    ],
];
