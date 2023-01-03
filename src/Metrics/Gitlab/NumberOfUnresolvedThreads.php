<?php

declare(strict_types=1);

namespace App\Metrics\Gitlab;

use App\Gitlab\Client\MergeRequest\Model\Details;
use App\Metrics\MetricInterface;
use App\Metrics\MetricResult;
use App\Metrics\StatsAggregator;

final class NumberOfUnresolvedThreads implements MetricInterface
{
    public function __construct(
        private readonly StatsAggregator $statsAggregator
    ) {
    }

    public function name(): string
    {
        return 'Number of unresolved threads';
    }

    public function result(Details $mergeRequestDetails): MetricResult
    {
        $stats = $this->statsAggregator->getResult($mergeRequestDetails);

        return new MetricResult(
            success: 0 === $stats->countUnresolvedThreads,
            expectedValue: '== 0',
            currentValue: (string) $stats->countUnresolvedThreads,
            description: <<<TXT
            Nombre de Threads non 'resolved'.
            TXT
        );
    }
}
