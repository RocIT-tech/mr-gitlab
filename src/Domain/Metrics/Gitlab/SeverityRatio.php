<?php

declare(strict_types=1);

namespace App\Domain\Metrics\Gitlab;

use App\Domain\Metrics\MetricCalculatorInterface;
use App\Domain\Metrics\MetricResult;
use App\Domain\Metrics\StatsAggregator;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Details;

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
