<?php

declare(strict_types=1);

namespace App\Metrics\Gitlab;

use App\Gitlab\Client\MergeRequest\Model\Details;
use App\Metrics\Severity;

final class SeverityWarningRatio extends SeverityRatio
{
    protected function getSeverity(): Severity
    {
        return Severity::SEVERITY_WARNING;
    }

    protected function getSeverityConstraint(): string
    {
        return '< 0.5';
    }

    protected function isSeverityConstraintSuccessful(float $currentSeverityRatio): bool
    {
        return $currentSeverityRatio < 0.5;
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
