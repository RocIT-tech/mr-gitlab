<?php

declare(strict_types=1);

namespace App\Metrics;

final class MetricResult
{
    public function __construct(
        public readonly bool $success,
        public readonly string $expectedValue,
        public readonly string $currentValue,
        public readonly string $description
    ) {
    }
}
