<?php

declare(strict_types=1);

namespace App\Metrics\Gitlab;

use App\Gitlab\Client\MergeRequest\Model\Details;
use App\Metrics\MetricCalculatorInterface;
use App\Metrics\MetricResult;
use App\Metrics\StatsAggregator;

abstract class SeverityRatio implements MetricCalculatorInterface
{
    public function __construct(
        protected readonly StatsAggregator $statsAggregator,
    ) {
    }

    abstract protected function getSeverityValue(Details $mergeRequestDetails): float;

    public function result(Details $mergeRequestDetails): MetricResult
    {
        $stats = $this->statsAggregator->getResult($mergeRequestDetails);

        $currentSeverityRatio = 0;
        if (0 !== $stats->numberOfThreads) {
            $currentSeverityRatio = $this->getSeverityValue($mergeRequestDetails) / $stats->numberOfThreads;
        }

        return new MetricResult(
            currentValue: (string) $currentSeverityRatio,
        );
    }
}
