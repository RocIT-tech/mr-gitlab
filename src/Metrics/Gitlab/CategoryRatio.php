<?php

declare(strict_types=1);

namespace App\Metrics\Gitlab;

use App\Gitlab\Client\MergeRequest\Model\Details;
use App\Metrics\MetricCalculatorInterface;
use App\Metrics\MetricResult;
use App\Metrics\StatsAggregator;

abstract class CategoryRatio implements MetricCalculatorInterface
{
    public function __construct(
        protected readonly StatsAggregator $statsAggregator,
    ) {
    }

    abstract protected function getCategoryValue(Details $mergeRequestDetails): float;

    public function result(Details $mergeRequestDetails): MetricResult
    {
        $stats = $this->statsAggregator->getResult($mergeRequestDetails);

        $currentCategoryRatio = 0;
        if (0 !== $stats->numberOfThreads) {
            $currentCategoryRatio = $this->getCategoryValue($mergeRequestDetails) / $stats->numberOfThreads;
        }

        return new MetricResult(
            currentValue: (string) $currentCategoryRatio,
        );
    }
}
