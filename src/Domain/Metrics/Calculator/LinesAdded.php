<?php

declare(strict_types=1);

namespace App\Domain\Metrics\Calculator;

use App\Domain\Metrics\Metric;
use App\Domain\Metrics\MetricCalculatorInterface;
use App\Domain\Metrics\MetricResult;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Details;

final class LinesAdded implements MetricCalculatorInterface
{
    public static function supportedMetric(): string
    {
        return Metric::LinesAdded->value;
    }

    public function getDefaultConstraint(): string
    {
        return 'value < 500';
    }

    public function result(Details $mergeRequestDetails): MetricResult
    {
        $linesAdded = $mergeRequestDetails->changes->totalDiff()->added;

        return new MetricResult(
            currentValue: (string) $linesAdded,
        );
    }

    public static function getDefaultPriority(): int
    {
        return 60;
    }
}
