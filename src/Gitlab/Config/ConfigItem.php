<?php

declare(strict_types=1);

namespace App\Gitlab\Config;

final class ConfigItem
{
    public function __construct(
        public readonly string            $name,
        public readonly string            $host,
        public readonly string            $token,
        public readonly ConfigItemMetrics $configMetrics,
    ) {
    }

    public function isMetricDisabled(string $name): bool
    {
        return $this->configMetrics->isMetricDisabled($name);
    }

    public function getConstraint(string $name): string
    {
        return $this->configMetrics->getConstraint($name);
    }
}
