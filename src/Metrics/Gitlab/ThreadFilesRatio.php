<?php

declare(strict_types=1);

namespace App\Metrics\Gitlab;

use App\Gitlab\Client\MergeRequest\Model\Details;
use App\Metrics\Metric;
use App\Metrics\MetricCalculatorInterface;
use App\Metrics\MetricResult;
use App\Metrics\StatsAggregator;
use function count;
use const INF;

final class ThreadFilesRatio implements MetricCalculatorInterface
{
    public function __construct(
        private readonly StatsAggregator $statsAggregator,
    ) {
    }

    public static function supportedMetric(): string
    {
        return Metric::ThreadsFilesRatio->value;
    }

    public function getDefaultConstraint(): string
    {
        return 'value < 1';
    }

    public function result(Details $mergeRequestDetails): MetricResult
    {
        $stats = $this->statsAggregator->getResult($mergeRequestDetails);

        $threadFileRatio = INF;
        if (0 !== count($mergeRequestDetails->changes)) {
            $threadFileRatio = $stats->numberOfThreads / count($mergeRequestDetails->changes);
        }

        return new MetricResult(
            currentValue: (string) $threadFileRatio,
        );
    }

    public static function getDefaultPriority(): int
    {
        return 90;
    }
}
