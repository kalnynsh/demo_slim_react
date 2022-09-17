<?php

declare(strict_types=1);

namespace App\Serializer;

use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @covers \App\Serializer\Normalizer
 *
 * @internal description
 */
final class NormalizerTest extends TestCase
{
    public function testValid(): void
    {
        $origin = $this->createMock(NormalizerInterface::class);
        $object= new stdClass();

        $object->name = 'John';

        $origin->expects(self::once())->method('normalize')
            ->with($object)
            ->willReturn(['name' => 'John']);

        $normalizer = new Normalizer($origin);

        $result = $normalizer->normalize($object);

        self::assertSame(['name' => 'John'], $result);
    }
}
