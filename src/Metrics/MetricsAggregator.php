<?php

declare(strict_types=1);

namespace App\Metrics;

use App\Gitlab\Client\MergeRequest\Model\Details;
use App\Gitlab\Config\Config;

final class MetricsAggregator
{
    /**
     * @param MetricInterface[] $metrics
     */
    public function __construct(
        private readonly iterable $metrics,
        private readonly Config   $config,
    ) {
    }

    /**
     * @return iterable<string, MetricResult>
     */
    public function getResult(Details $mergeRequestDetails): iterable
    {
        $config = $this->config->getByHost($mergeRequestDetails->web_url);

        foreach ($this->metrics as $metric) {
            if ($config->isMetricDisabled($metric->name())) {
                continue;
            }

            yield $metric->name() => $metric->result($mergeRequestDetails);
        }
    }
}
