<?php

declare(strict_types=1);

namespace App\Metrics\Gitlab;

use App\Gitlab\Client\MergeRequest\Model\Details;
use App\Metrics\MetricInterface;
use App\Metrics\MetricResult;
use function count;

final class FilesChanged implements MetricInterface
{
    public function name(): string
    {
        return 'Files Changed';
    }

    public function result(Details $mergeRequestDetails): MetricResult
    {
        $filesChanged = count($mergeRequestDetails->changes);

        return new MetricResult(
            success: $filesChanged < 30,
            expectedValue: '< 30',
            currentValue: (string) $filesChanged,
            description: <<<TXT
            Nombre de fichiers changés
            TXT
        );
    }

    public static function getDefaultPriority(): int
    {
        return 70;
    }
}
