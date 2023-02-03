<?php

declare(strict_types=1);

namespace App\Domain\Metrics\Gitlab;

use App\Domain\Metrics\Metric;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Details;

final class SeverityWarningRatio extends SeverityRatio
{
    public static function supportedMetric(): string
    {
        return Metric::WarningRatio->value;
    }

    public function getDefaultConstraint(): string
    {
        return 'value < 0.5';
    }

    public static function getDefaultPriority(): int
    {
        return 20;
    }

    protected function getSeverityValue(Details $mergeRequestDetails): float
    {
        return $this->statsAggregator->getResult($mergeRequestDetails)->countSeverityWarning;
    }
}
