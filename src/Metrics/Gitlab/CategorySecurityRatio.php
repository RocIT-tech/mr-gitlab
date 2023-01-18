<?php

declare(strict_types=1);

namespace App\Metrics\Gitlab;

use App\Gitlab\Client\MergeRequest\Model\Details;
use App\Metrics\Metric;

final class CategorySecurityRatio extends CategoryRatio
{
    public static function supportedMetric(): string
    {
        return Metric::SecurityRatio->value;
    }

    public function getDefaultConstraint(): string
    {
        return 'value == 0';
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
