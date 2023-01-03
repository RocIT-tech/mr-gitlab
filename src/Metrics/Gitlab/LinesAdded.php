<?php

declare(strict_types=1);

namespace App\Metrics\Gitlab;

use App\Gitlab\Client\MergeRequest\Model\Details;
use App\Metrics\MetricInterface;
use App\Metrics\MetricResult;

final class LinesAdded implements MetricInterface
{
    public function name(): string
    {
        return 'Lines Added';
    }

    public function result(Details $mergeRequestDetails): MetricResult
    {
        $linesAdded = $mergeRequestDetails->changes->totalDiff()->added;

        return new MetricResult(
            success: $linesAdded < 500,
            expectedValue: '< 500',
            currentValue: (string) $linesAdded,
            description: <<<TXT
            Nombre de lignes ajoutées
            TXT
        );
    }

    public static function getDefaultPriority(): int
    {
        return 60;
    }
}
