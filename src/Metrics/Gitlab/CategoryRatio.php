<?php

declare(strict_types=1);

namespace App\Metrics\Gitlab;

use App\Gitlab\Client\MergeRequest\Model\Details;
use App\Metrics\Category;
use App\Metrics\MetricCalculatorInterface;
use App\Metrics\MetricResult;
use App\Metrics\StatsAggregator;
use function strtolower;
use function ucfirst;

abstract class CategoryRatio implements MetricCalculatorInterface
{
    public function __construct(
        protected readonly StatsAggregator $statsAggregator,
    ) {
    }

    abstract protected function getCategory(): Category;

    abstract protected function getCategoryValue(Details $mergeRequestDetails): float;

    public function name(): string
    {
        $category = ucfirst(strtolower($this->getCategory()->value));

        return "{$category} Ratio";
    }

    public function description(): string
    {
        return "Nombre de threads de catégorie \"{$this->getCategory()->value}\" / Nombre de threads";
    }

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
