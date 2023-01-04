<?php

declare(strict_types=1);

namespace App\Metrics;

use App\Gitlab\Client\MergeRequest\Model\Details;
use App\Gitlab\Config\Config;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class MetricsAggregator
{
    /**
     * @param MetricCalculatorInterface[] $metrics
     */
    public function __construct(
        private readonly iterable $metrics,
        private readonly Config   $config,
    ) {
    }

    /**
     * @return iterable<string, ValidatedMetric>
     */
    public function getResult(Details $mergeRequestDetails): iterable
    {
        $config = $this->config->getByHost($mergeRequestDetails->web_url);

        foreach ($this->metrics as $metric) {
            if ($config->isMetricDisabled($metric->name())) {
                continue;
            }

            $metricName   = $metric->name();
            $constraint   = $config->getConstraint($metricName) ?? $metric->getDefaultConstraint();
            $metricResult = $metric->result($mergeRequestDetails);

            $validator = new ExpressionLanguage();

            yield $metricName => new ValidatedMetric(
                name: $metricName,
                description: $metric->description(),
                constraint: $constraint,
                currentValue: $metricResult,
                success: $validator->evaluate($constraint, ['value' => $metricResult->currentValue])
            );
        }
    }
}
