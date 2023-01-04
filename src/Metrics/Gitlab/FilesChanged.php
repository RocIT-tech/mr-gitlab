<?php

declare(strict_types=1);

namespace App\Metrics\Gitlab;

use App\Gitlab\Client\MergeRequest\Model\Details;
use App\Metrics\MetricCalculatorInterface;
use App\Metrics\MetricResult;
use function count;

final class FilesChanged implements MetricCalculatorInterface
{
    public function name(): string
    {
        return 'Files Changed';
    }

    public function description(): string
    {
        return 'Nombre de fichiers changés';
    }

    public function getDefaultConstraint(): string
    {
        return 'value < 30';
    }

    public function result(Details $mergeRequestDetails): MetricResult
    {
        $filesChanged = count($mergeRequestDetails->changes);

        return new MetricResult(
            currentValue: (string) $filesChanged,
        );
    }

    public static function getDefaultPriority(): int
    {
        return 70;
    }
}
