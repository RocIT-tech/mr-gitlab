<?php

declare(strict_types=1);

namespace App\Metrics;

use App\Gitlab\Client\MergeRequest\Model\Details;

final class MetricsAggregator
{
    /**
     * @param MetricInterface[] $metrics
     */
    public function __construct(
        private readonly iterable $metrics
    ) {
    }

    public function getResult(Details $mergeRequestDetails): iterable
    {
        foreach ($this->metrics as $metric) {
            yield $metric->name() => $metric->result($mergeRequestDetails);
        }
    }
}
