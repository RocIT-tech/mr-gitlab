<?php

declare(strict_types=1);

namespace App\Gitlab\Config;

use function array_key_exists;

final class ConfigItemMetrics
{
    /** @var array<string> */
    private array $disabledMetrics = [];

    /** @var array<string, string> */
    private array $constraints = [];

    /**
     * @param array<string, array{enabled?: bool, constraint?: string}> $metricsConfigurations
     */
    public function __construct(
        array $metricsConfigurations = [],
    ) {
        foreach ($metricsConfigurations as $metricName => $metricConfiguration) {
            if (false === ($metricConfiguration['enabled'] ?? true)) {
                $this->disabledMetrics[$metricName] = $metricName;
            } elseif (array_key_exists('constraint', $metricConfiguration) === true) {
                $this->constraints[$metricName] = $metricConfiguration['constraint'];
            }
        }
    }

    public function isMetricDisabled(string $name): bool
    {
        return array_key_exists($name, $this->disabledMetrics);
    }

    public function hasConstraint(string $name): bool
    {
        if ($this->isMetricDisabled($name) === true) {
            return false;
        }

        return array_key_exists($name, $this->constraints);
    }

    public function getConstraint(string $name): ?string
    {
        if ($this->hasConstraint($name) === false) {
            return null;
        }

        return $this->constraints[$name];
    }
}
