<?php

declare(strict_types=1);

namespace App\Metrics\Gitlab;

use App\Gitlab\Client\MergeRequest\Model\Details;
use App\Metrics\Category;
use App\Metrics\MetricInterface;
use App\Metrics\MetricResult;
use App\Metrics\StatsAggregator;
use function strtolower;
use function ucfirst;

abstract class CategoryRatio implements MetricInterface
{
    public function __construct(
        protected readonly StatsAggregator $statsAggregator
    ) {
    }

    abstract protected function getCategory(): Category;

    abstract protected function getCategoryConstraint(): string;

    abstract protected function getCategoryValue(Details $mergeRequestDetails): float;

    abstract protected function isCategoryConstraintSuccessful(float $currentCategoryRatio): bool;

    public function name(): string
    {
        $category = ucfirst(strtolower($this->getCategory()->value));

        return "{$category} Ratio";
    }

    public function result(Details $mergeRequestDetails): MetricResult
    {
        $stats = $this->statsAggregator->getResult($mergeRequestDetails);

        $currentCategoryRatio = 0;
        if (0 !== $stats->numberOfThreads) {
            $currentCategoryRatio = $this->getCategoryValue($mergeRequestDetails) / $stats->numberOfThreads;
        }

        return new MetricResult(
            success: $this->isCategoryConstraintSuccessful((float) $currentCategoryRatio),
            expectedValue: $this->getCategoryConstraint(),
            currentValue: (string) $currentCategoryRatio,
            description: <<<TXT
            Nombre de threads de catégorie "{$this->getCategory()->value}" / Nombre de threads
            TXT
        );
    }
}
