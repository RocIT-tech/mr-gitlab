<?php

declare(strict_types=1);

namespace App\Metrics\Gitlab;

use App\Gitlab\Client\MergeRequest\Model\Details;
use App\Metrics\Category;

final class CategoryReadabilityRatio extends CategoryRatio
{
    public function getDefaultConstraint(): string
    {
        return 'value < 1';
    }

    protected function getCategory(): Category
    {
        return Category::CATEGORY_READABILITY;
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
