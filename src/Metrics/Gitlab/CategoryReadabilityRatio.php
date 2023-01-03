<?php

declare(strict_types=1);

namespace App\Metrics\Gitlab;

use App\Gitlab\Client\MergeRequest\Model\Details;
use App\Metrics\Category;

final class CategoryReadabilityRatio extends CategoryRatio
{
    protected function getCategory(): Category
    {
        return Category::CATEGORY_READABILITY;
    }

    protected function getCategoryConstraint(): string
    {
        return '< 1';
    }

    protected function isCategoryConstraintSuccessful(float $currentCategoryRatio): bool
    {
        return $currentCategoryRatio < 1.0;
    }

    public static function getDefaultPriority(): int
    {
        return 15;
    }

    protected function getCategoryValue(Details $mergeRequestDetails): float
    {
        return $this->statsAggregator->getResult($mergeRequestDetails)->countCategoryReadability;
    }
}
