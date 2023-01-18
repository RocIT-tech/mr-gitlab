<?php

declare(strict_types=1);

namespace App\Metrics;

use App\Gitlab\Client\MergeRequest\Model\Details;
use App\Gitlab\Config\Config;
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
        private readonly Config          $config,
    ) {
    }

    public function getResult(Details $mergeRequestDetails): ValidatedMetrics
    {
        return new ValidatedMetrics(
            $this->statsAggregator->getResult($mergeRequestDetails),
            $this->getResultIterator($mergeRequestDetails)
        );
    }

    /**
     * @return iterable<string, ValidatedMetric>
     */
    private function getResultIterator(Details $mergeRequestDetails): iterable
    {
        $config = $this->config->getByHost($mergeRequestDetails->web_url);

        $validator = new ExpressionLanguage();

        foreach (Metric::cases() as $metric) {
            if ($config->isMetricDisabled($metric->name())) {
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
