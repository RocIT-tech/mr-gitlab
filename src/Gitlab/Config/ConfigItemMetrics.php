<?php

declare(strict_types=1);

namespace App\Gitlab\Config;

use function array_key_exists;

final class ConfigItemMetrics
{
    /** @var array<string> */
    private array $disabledMetrics = [];

    public function __construct(
        array $metricsConfigurations = [],
    ) {
        foreach ($metricsConfigurations as $metricName => $metricConfiguration) {
            if (false === ($metricConfiguration['enabled'] ?? true)) {
                $this->disabledMetrics[$metricName] = $metricName;
            }
        }
    }

    public function isMetricDisabled(string $name): bool
    {
        return array_key_exists($name, $this->disabledMetrics);
    }
}
