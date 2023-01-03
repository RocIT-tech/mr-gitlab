<?php

declare(strict_types=1);

namespace App\Metrics\Gitlab;

use App\Gitlab\Client\MergeRequest\Model\Details;
use App\Metrics\MetricInterface;
use App\Metrics\MetricResult;
use function count;

final class NumberOfThreads implements MetricInterface
{
    public function name(): string
    {
        return 'Number of Threads';
    }

    public function result(Details $mergeRequestDetails): MetricResult
    {
        $numberOfThreads = count($mergeRequestDetails->threads);

        return new MetricResult(
            success: $numberOfThreads < 30,
            expectedValue: '< 30',
            currentValue: (string) $numberOfThreads,
            description: <<<TXT
            Nombre de threads ouvert
            TXT
        );
    }

    public static function getDefaultPriority(): int
    {
        return 100;
    }
}
