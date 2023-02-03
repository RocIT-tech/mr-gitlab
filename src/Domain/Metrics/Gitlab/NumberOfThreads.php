<?php

declare(strict_types=1);

namespace App\Domain\Metrics\Gitlab;

use App\Domain\Metrics\Metric;
use App\Domain\Metrics\MetricCalculatorInterface;
use App\Domain\Metrics\MetricResult;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Details;
use function count;

final class NumberOfThreads implements MetricCalculatorInterface
{
    public static function supportedMetric(): string
    {
        return Metric::NumberOfThreads->value;
    }

    public function getDefaultConstraint(): string
    {
        return 'value < 30';
    }

    public function result(Details $mergeRequestDetails): MetricResult
    {
        $numberOfThreads = count($mergeRequestDetails->threads);

        return new MetricResult(
            currentValue: (string) $numberOfThreads,
        );
    }

    public static function getDefaultPriority(): int
    {
        return 100;
    }
}
