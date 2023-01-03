<?php

declare(strict_types=1);

namespace App\Metrics\Gitlab;

use App\Gitlab\Client\MergeRequest\Model\Details;
use App\Metrics\MetricInterface;
use App\Metrics\MetricResult;
use App\Metrics\StatsAggregator;
use function count;
use const INF;

final class ThreadFilesRatio implements MetricInterface
{
    public function __construct(
        private readonly StatsAggregator $statsAggregator
    ) {
    }

    public function name(): string
    {
        return 'Thread / Files Ratio';
    }

    public function result(Details $mergeRequestDetails): MetricResult
    {
        $stats = $this->statsAggregator->getResult($mergeRequestDetails);

        $threadFileRatio = INF;
        if (0 !== count($mergeRequestDetails->changes)) {
            $threadFileRatio = $stats->numberOfThreads / count($mergeRequestDetails->changes);
        }

        return new MetricResult(
            success: $threadFileRatio < 1,
            expectedValue: '< 1',
            currentValue: (string) $threadFileRatio,
            description: <<<TXT
            Ratio entre le nombre de thread ouverts et le nombre de fichiers modifiés
            TXT
        );
    }

    public static function getDefaultPriority(): int
    {
        return 90;
    }
}
