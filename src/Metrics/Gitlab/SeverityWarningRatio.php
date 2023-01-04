<?php

declare(strict_types=1);

namespace App\Metrics\Gitlab;

use App\Gitlab\Client\MergeRequest\Model\Details;
use App\Metrics\Severity;

final class SeverityWarningRatio extends SeverityRatio
{
    public function getDefaultConstraint(): string
    {
        return 'value < 0.5';
    }

    protected function getSeverity(): Severity
    {
        return Severity::SEVERITY_WARNING;
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
