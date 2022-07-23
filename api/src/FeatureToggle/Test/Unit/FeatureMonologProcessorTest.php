<?php

declare(strict_types=1);

namespace App\FeatureToggle\Test\Unit;

use App\FeatureToggle\FeaturesContext;
use App\FeatureToggle\FeaturesMonologProcessor;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class FeatureMonologProcessorTest extends TestCase
{
    public function testProcess(): void
    {
        $context = $this->createStub(FeaturesContext::class);
        $context->method('getAllEnabled')->willReturn($source = ['ONE', 'TWO']);

        /** @psalm-suppress InvalidArgument */
        $processor = new FeaturesMonologProcessor($context);

        $record = [
            'channel' => 'Unit test',
            'context' => [self::class],
            'datetime' => new DateTimeImmutable(),
            'extra' => [],
            'level' => 500,
            'level_name' => 'INFO',
            'message' => 'Message',
        ];

        $result = $processor($record);

        self::assertEquals(\array_merge_recursive($record, [
            'extra' => [
                'features' => $source,
            ],
        ]), $result);
    }
}
