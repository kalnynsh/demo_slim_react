<?php

declare(strict_types=1);

namespace App\FeatureToggle;

use Monolog\Processor\ProcessorInterface;

class FeaturesMonologProcessor implements ProcessorInterface
{
    private FeaturesContext $context;

    public function __construct(FeaturesContext $context)
    {
        $this->context = $context;
    }

    /**
     * @psalm-suppress MoreSpecificReturnType, LessSpecificReturnStatement
     * @psalm-suppress LessSpecificImplementedReturnType
     *
     * @psalm-param array{
     *  channel: string,
     *  context: array<array-key, mixed>,
     *  datetime: \DateTimeImmutable,
     *  extra: array<array-key, mixed>,
     *  level: 100|200|250|300|400|500|550|600,
     *  level_name: "ALERT"|"CRITICAL"|"DEBUG"|"EMERGENCY"|"ERROR"|"INFO"|"NOTICE"|"WARNING",
     *  message: string
     * } $record
     *
     */
    public function __invoke(array $record): array
    {
        return \array_merge_recursive($record, [
            'extra' => [
                'features' => $this->context->getAllEnabled(),
            ]
        ]);
    }
}
