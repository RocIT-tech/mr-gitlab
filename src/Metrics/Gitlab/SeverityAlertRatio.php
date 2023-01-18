<?php

declare(strict_types=1);

namespace App\Metrics\Gitlab;

use App\Gitlab\Client\MergeRequest\Model\Details;
use App\Metrics\Metric;

final class SeverityAlertRatio extends SeverityRatio
{
    public static function supportedMetric(): string
    {
        return Metric::AlertRatio->value;
    }

    public function getDefaultConstraint(): string
    {
        return 'value == 0';
    }

    public static function getDefaultPriority(): int
    {
        return 30;
    }

    protected function getSeverityValue(Details $mergeRequestDetails): float
    {
        return $this->statsAggregator->getResult($mergeRequestDetails)->countSeverityAlert;
    }
}
