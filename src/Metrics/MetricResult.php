<?php

declare(strict_types=1);

namespace App\Metrics;

final class MetricResult
{
    public function __construct(
        public readonly string $currentValue,
    ) {
    }
}
