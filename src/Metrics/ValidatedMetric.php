<?php

declare(strict_types=1);

namespace App\Metrics;

final class ValidatedMetric
{
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly string $constraint,
        public readonly MetricResult $currentValue,
        public readonly bool $success,
    ) {
    }
}
