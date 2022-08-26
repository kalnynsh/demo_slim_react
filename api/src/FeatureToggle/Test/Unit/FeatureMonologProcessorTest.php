<?php

declare(strict_types=1);

namespace App\FeatureToggle\Test\Unit;

use App\FeatureToggle\FeaturesContext;
use App\FeatureToggle\FeaturesMonologProcessor;
use DateTimeImmutable;
use Monolog\Logger;
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
        $date = new DateTimeImmutable();

        $record = [
            'channel' => 'Unit test',
            'message' => 'Message',
            'context' => ['name' => 'value'],
            'datetime' => $date,
            'level' => Logger::WARNING,
            'level_name' => 'WARNING',
            'extra' => ['param' => 'value'],
        ];

        $result = $processor($record);

        self::assertEquals([
                'channel' => 'Unit test',
                'message' => 'Message',
                'context' => ['name' => 'value'],
                'datetime' => $date,
                'level' => Logger::WARNING,
                'level_name' => 'WARNING',
                'extra' => [
                    'param' => 'value',
                    'features' => $source,
                ],
        ], $result);
    }
}
