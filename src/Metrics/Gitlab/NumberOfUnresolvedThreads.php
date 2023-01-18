<?php

declare(strict_types=1);

namespace App\Metrics\Gitlab;

use App\Gitlab\Client\MergeRequest\Model\Details;
use App\Metrics\Metric;
use App\Metrics\MetricCalculatorInterface;
use App\Metrics\MetricResult;
use App\Metrics\StatsAggregator;

final class NumberOfUnresolvedThreads implements MetricCalculatorInterface
{
    public function __construct(
        private readonly StatsAggregator $statsAggregator,
    ) {
    }

    public static function supportedMetric(): string
    {
        return Metric::NumberOfUnresolvedThreads->value;
    }

    public function getDefaultConstraint(): string
    {
        return 'value == 0';
    }

    public function result(Details $mergeRequestDetails): MetricResult
    {
        $stats = $this->statsAggregator->getResult($mergeRequestDetails);

        return new MetricResult(
            currentValue: (string) $stats->countUnresolvedThreads,
        );
    }
}
