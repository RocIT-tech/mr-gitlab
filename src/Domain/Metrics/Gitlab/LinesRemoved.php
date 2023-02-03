<?php

declare(strict_types=1);

namespace App\Domain\Metrics\Gitlab;

use App\Domain\Metrics\Metric;
use App\Domain\Metrics\MetricCalculatorInterface;
use App\Domain\Metrics\MetricResult;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Details;
use function abs;

final class LinesRemoved implements MetricCalculatorInterface
{
    public static function supportedMetric(): string
    {
        return Metric::LinesRemoved->value;
    }

    public function getDefaultConstraint(): string
    {
        return 'value < 500';
    }

    public function result(Details $mergeRequestDetails): MetricResult
    {
        $linesRemoved = abs($mergeRequestDetails->changes->totalDiff()->removed);

        return new MetricResult(
            currentValue: (string) $linesRemoved,
        );
    }

    public static function getDefaultPriority(): int
    {
        return 50;
    }
}
