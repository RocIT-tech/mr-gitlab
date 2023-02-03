<?php

declare(strict_types=1);

namespace App\Domain\Tenant;

use SensitiveParameter;

final class Config
{
    public string $token;

    public function __construct(
        public readonly string        $name,
        public readonly string        $host,
        #[SensitiveParameter] string  $token,
        public readonly ConfigMetrics $configMetrics,
    ) {
        $this->token = $token;
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
