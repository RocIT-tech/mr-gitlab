<?php

declare(strict_types=1);

namespace App\Metrics\Gitlab;

use App\Gitlab\Client\MergeRequest\Model\Details;
use App\Metrics\MetricInterface;
use App\Metrics\MetricResult;
use App\Metrics\Severity;
use App\Metrics\StatsAggregator;
use function strtolower;
use function ucfirst;

abstract class SeverityRatio implements MetricInterface
{
    public function __construct(
        protected readonly StatsAggregator $statsAggregator
    ) {
    }

    abstract protected function getSeverity(): Severity;

    abstract protected function getSeverityConstraint(): string;

    abstract protected function getSeverityValue(Details $mergeRequestDetails): float;

    abstract protected function isSeverityConstraintSuccessful(float $currentSeverityRatio): bool;

    public function name(): string
    {
        $severity = ucfirst(strtolower($this->getSeverity()->value));

        return "{$severity} Ratio";
    }

    public function result(Details $mergeRequestDetails): MetricResult
    {
        $stats = $this->statsAggregator->getResult($mergeRequestDetails);

        $currentSeverityRatio = 0;
        if (0 !== $stats->numberOfThreads) {
            $currentSeverityRatio = $this->getSeverityValue($mergeRequestDetails) / $stats->numberOfThreads;
        }

        return new MetricResult(
            success: $this->isSeverityConstraintSuccessful((float) $currentSeverityRatio),
            expectedValue: $this->getSeverityConstraint(),
            currentValue: (string) $currentSeverityRatio,
            description: <<<TXT
            Nombre de threads de type "{$this->getSeverity()->value}" / Nombre de threads
            TXT
        );
    }
}
