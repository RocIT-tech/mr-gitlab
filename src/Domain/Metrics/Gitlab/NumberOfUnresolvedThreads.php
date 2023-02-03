<?php

declare(strict_types=1);

namespace App\Domain\Metrics\Gitlab;

use App\Domain\Metrics\Metric;
use App\Domain\Metrics\MetricCalculatorInterface;
use App\Domain\Metrics\MetricResult;
use App\Domain\Metrics\StatsAggregator;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Details;

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
