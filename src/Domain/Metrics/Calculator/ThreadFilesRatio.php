<?php

declare(strict_types=1);

namespace App\Domain\Metrics\Calculator;

use App\Domain\Metrics\Metric;
use App\Domain\Metrics\MetricCalculatorInterface;
use App\Domain\Metrics\MetricResult;
use App\Domain\Metrics\StatsAggregator;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Details;
use function count;
use const INF;

final class ThreadFilesRatio implements MetricCalculatorInterface
{
    public function __construct(
        private readonly StatsAggregator $statsAggregator,
    ) {
    }

    public static function supportedMetric(): string
    {
        return Metric::ThreadsFilesRatio->value;
    }

    public function getDefaultConstraint(): string
    {
        return 'value < 1';
    }

    public function result(Details $mergeRequestDetails): MetricResult
    {
        $stats = $this->statsAggregator->getResult($mergeRequestDetails);

        $threadFileRatio = INF;
        if (0 !== count($mergeRequestDetails->changes)) {
            $threadFileRatio = $stats->numberOfThreads / count($mergeRequestDetails->changes);
        }

        return new MetricResult(
            currentValue: (string) $threadFileRatio,
        );
    }

    public static function getDefaultPriority(): int
    {
        return 90;
    }
}
