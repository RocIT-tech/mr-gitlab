<?php

declare(strict_types=1);

namespace App\Metrics\Gitlab;

use App\Gitlab\Client\MergeRequest\Model\Details;
use App\Metrics\Metric;
use App\Metrics\MetricCalculatorInterface;
use App\Metrics\MetricResult;
use App\Metrics\StatsAggregator;

final class RepliesPerThreadRatio implements MetricCalculatorInterface
{
    public function __construct(
        private readonly StatsAggregator $statsAggregator,
    ) {
    }

    public static function supportedMetric(): string
    {
        return Metric::RepliesPerThreadRatio->value;
    }

    public function getDefaultConstraint(): string
    {
        return 'value < 2.5';
    }

    public function result(Details $mergeRequestDetails): MetricResult
    {
        $stats = $this->statsAggregator->getResult($mergeRequestDetails);

        $repliesPerThreadRatio = 0;
        if (0 !== $stats->numberOfThreads && 0 !== $stats->numberOfReplies) {
            $repliesPerThreadRatio = $stats->numberOfReplies / $stats->numberOfThreads;
        }

        return new MetricResult(
            currentValue: (string) $repliesPerThreadRatio,
        );
    }

    public static function getDefaultPriority(): int
    {
        return 40;
    }
}
