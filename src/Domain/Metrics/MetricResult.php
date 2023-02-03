<?php

declare(strict_types=1);

namespace App\Domain\Metrics;

final class MetricResult
{
    public function __construct(
        public readonly string $currentValue,
    ) {
    }
}
