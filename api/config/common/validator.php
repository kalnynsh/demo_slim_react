<?php

declare(strict_types=1);

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Psr\Container\ContainerInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Contracts\Translation\TranslatorInterface;

return [
    ValidatorInterface::class => static function (ContainerInterface $container): ValidatorInterface {
        /** @psalm-suppress DeprecatedMethod */
        AnnotationRegistry::registerLoader('class_exists');

        $translator = $container->get(TranslatorInterface::class);

        return Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->addDefaultDoctrineAnnotationReader()
            ->setTranslator($translator)
            ->setTranslationDomain('validators')
            ->getValidator();
    },
];
