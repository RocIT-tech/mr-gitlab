<?php

declare(strict_types=1);

namespace App\Metrics\Gitlab;

use App\Gitlab\Client\MergeRequest\Model\Details;
use App\Metrics\MetricCalculatorInterface;
use App\Metrics\MetricResult;

final class LinesAdded implements MetricCalculatorInterface
{
    public function name(): string
    {
        return 'Lines Added';
    }

    public function description(): string
    {
        return 'Nombre de lignes ajoutées';
    }

    public function getDefaultConstraint(): string
    {
        return 'value < 500';
    }

    public function result(Details $mergeRequestDetails): MetricResult
    {
        $linesAdded = $mergeRequestDetails->changes->totalDiff()->added;

        return new MetricResult(
            currentValue: (string) $linesAdded,
        );
    }

    public static function getDefaultPriority(): int
    {
        return 60;
    }
}
