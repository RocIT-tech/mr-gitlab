<?php

declare(strict_types=1);

namespace App\Metrics\Gitlab;

use App\Gitlab\Client\MergeRequest\Model\Details;
use App\Metrics\Category;

final class CategorySecurityRatio extends CategoryRatio
{
    public function getDefaultConstraint(): string
    {
        return 'value == 0';
    }

    protected function getCategory(): Category
    {
        return Category::CATEGORY_SECURITY;
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
