<?php

declare(strict_types=1);

namespace App\Metrics\Gitlab;

use App\Gitlab\Client\MergeRequest\Model\Details;
use App\Metrics\MetricInterface;
use App\Metrics\MetricResult;
use App\Metrics\StatsAggregator;

final class RepliesPerThreadRatio implements MetricInterface
{
    public function __construct(
        private readonly StatsAggregator $statsAggregator
    ) {
    }

    public function name(): string
    {
        return 'Replies per Thread Ratio';
    }

    public function result(Details $mergeRequestDetails): MetricResult
    {
        $stats = $this->statsAggregator->getResult($mergeRequestDetails);

        $repliesPerThreadRatio = 0;
        if (0 !== $stats->numberOfThreads && 0 !== $stats->numberOfReplies) {
            $repliesPerThreadRatio = $stats->numberOfReplies / $stats->numberOfThreads;
        }

        return new MetricResult(
            success: $repliesPerThreadRatio < 2.5,
            expectedValue: '< 2.5',
            currentValue: (string) $repliesPerThreadRatio,
            description: <<<TXT
            Nombre de réponses / nombre de threads
            TXT
        );
    }

    public static function getDefaultPriority(): int
    {
        return 40;
    }
}
