<?php

declare(strict_types=1);

namespace App\Domain\Metrics\Calculator;

use App\Domain\Metrics\MetricCalculatorInterface;
use App\Domain\Metrics\MetricResult;
use App\Domain\Metrics\StatsAggregator;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Details;

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
