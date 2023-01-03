<?php

declare(strict_types=1);

namespace App\Metrics\Gitlab;

use App\Gitlab\Client\MergeRequest\Model\Details;
use App\Metrics\MetricInterface;
use App\Metrics\MetricResult;
use function abs;

final class LinesRemoved implements MetricInterface
{
    public function name(): string
    {
        return 'Lines Removed';
    }

    public function result(Details $mergeRequestDetails): MetricResult
    {
        $linesRemoved = abs($mergeRequestDetails->changes->totalDiff()->removed);

        return new MetricResult(
            success: $linesRemoved < 500,
            expectedValue: '< 500',
            currentValue: (string) $linesRemoved,
            description: <<<TXT
            Nombre de lignes supprimées
            TXT
        );
    }

    public static function getDefaultPriority(): int
    {
        return 50;
    }
}
