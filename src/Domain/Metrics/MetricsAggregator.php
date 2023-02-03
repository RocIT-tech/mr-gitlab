<?php

declare(strict_types=1);

namespace App\Domain\Metrics;

use App\Domain\Tenant\Config;
use App\Infrastructure\Gitlab\Client\MergeRequest\Model\Details;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class MetricsAggregator
{
    /**
     * @param ServiceLocator<MetricCalculatorInterface> $metrics
     */
    public function __construct(
        private readonly StatsAggregator $statsAggregator,
        private readonly ServiceLocator  $metrics,
    ) {
    }

    public function getResult(Config $config, Details $mergeRequestDetails): ValidatedMetrics
    {
        return new ValidatedMetrics(
            $this->statsAggregator->getResult($mergeRequestDetails),
            $this->getResultIterator($config, $mergeRequestDetails)
        );
    }

    /**
     * @return iterable<string, ValidatedMetric>
     */
    private function getResultIterator(Config $config, Details $mergeRequestDetails): iterable
    {
        $validator = new ExpressionLanguage();

        foreach (Metric::cases() as $metric) {
            if ($config->isMetricDisabled($metric->value)) {
                continue;
            }

            /** @var MetricCalculatorInterface $metricCalculator */
            $metricCalculator = $this->metrics->get($metric->value);
            $constraint       = $config->getConstraint($metric->value);
            $metricResult     = $metricCalculator->result($mergeRequestDetails);

            yield $metric->name() => ValidatedMetric::forMetric(
                metric: $metric,
                constraint: $constraint,
                currentValue: $metricResult,
                success: $validator->evaluate($constraint, ['value' => $metricResult->currentValue]),
            );
        }
    }
}
