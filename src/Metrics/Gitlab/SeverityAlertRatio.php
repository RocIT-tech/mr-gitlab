<?php

declare(strict_types=1);

namespace App\Metrics\Gitlab;

use App\Gitlab\Client\MergeRequest\Model\Details;
use App\Metrics\Severity;

final class SeverityAlertRatio extends SeverityRatio
{
    protected function getSeverity(): Severity
    {
        return Severity::SEVERITY_ALERT;
    }

    protected function getSeverityConstraint(): string
    {
        return '== 0';
    }

    protected function isSeverityConstraintSuccessful(float $currentSeverityRatio): bool
    {
        return 0.0 === $currentSeverityRatio;
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
