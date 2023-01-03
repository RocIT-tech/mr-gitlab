<?php

declare(strict_types=1);

namespace App\Metrics\Gitlab;

use App\Gitlab\Client\MergeRequest\Model\Details;
use App\Metrics\Category;

final class CategorySecurityRatio extends CategoryRatio
{
    protected function getCategory(): Category
    {
        return Category::CATEGORY_SECURITY;
    }

    protected function getCategoryConstraint(): string
    {
        return '== 0';
    }

    protected function isCategoryConstraintSuccessful(float $currentCategoryRatio): bool
    {
        return 0.0 === $currentCategoryRatio;
    }

    public static function getDefaultPriority(): int
    {
        return 10;
    }

    protected function getCategoryValue(Details $mergeRequestDetails): float
    {
        return $this->statsAggregator->getResult($mergeRequestDetails)->countCategorySecurity;
    }
}
