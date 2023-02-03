<?php

declare(strict_types=1);

namespace App\Domain\Metrics;

final class ValidatedMetric
{
    private function __construct(
        public readonly string       $name,
        public readonly string       $description,
        public readonly string       $constraint,
        public readonly MetricResult $currentValue,
        public readonly bool         $success,
    ) {
    }

    public static function forMetric(
        Metric       $metric,
        string       $constraint,
        MetricResult $currentValue,
        bool         $success,
    ): self {
        return new self(
            name: $metric->name(),
            description: $metric->description(),
            constraint: $constraint,
            currentValue: $currentValue,
            success: $success,
        );
    }
}
