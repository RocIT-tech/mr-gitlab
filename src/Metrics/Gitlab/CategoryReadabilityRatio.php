<?php

declare(strict_types=1);

namespace App\Metrics\Gitlab;

use App\Gitlab\Client\MergeRequest\Model\Details;
use App\Metrics\Metric;

final class CategoryReadabilityRatio extends CategoryRatio
{
    public static function supportedMetric(): string
    {
        return Metric::ReadabilityRatio->value;
    }

    public function getDefaultConstraint(): string
    {
        return 'value < 1';
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
