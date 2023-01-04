<?php

declare(strict_types=1);

namespace App\Metrics\Gitlab;

use App\Gitlab\Client\MergeRequest\Model\Details;
use App\Metrics\MetricCalculatorInterface;
use App\Metrics\MetricResult;
use App\Metrics\Severity;
use App\Metrics\StatsAggregator;
use function strtolower;
use function ucfirst;

abstract class SeverityRatio implements MetricCalculatorInterface
{
    public function __construct(
        protected readonly StatsAggregator $statsAggregator,
    ) {
    }

    abstract protected function getSeverity(): Severity;

    abstract protected function getSeverityValue(Details $mergeRequestDetails): float;

    public function name(): string
    {
        $severity = ucfirst(strtolower($this->getSeverity()->value));

        return "{$severity} Ratio";
    }

    public function description(): string
    {
        return "Nombre de threads de type \"{$this->getSeverity()->value}\" / Nombre de threads";
    }

    public function result(Details $mergeRequestDetails): MetricResult
    {
        $stats = $this->statsAggregator->getResult($mergeRequestDetails);

        $currentSeverityRatio = 0;
        if (0 !== $stats->numberOfThreads) {
            $currentSeverityRatio = $this->getSeverityValue($mergeRequestDetails) / $stats->numberOfThreads;
        }

        return new MetricResult(
            currentValue: (string) $currentSeverityRatio,
        );
    }
}
