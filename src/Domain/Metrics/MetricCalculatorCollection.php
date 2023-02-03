<?php

declare(strict_types=1);

namespace App\Domain\Metrics;

use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Contracts\Service\ServiceProviderInterface;
use function array_keys;
use function count;

/**
 * @implements ServiceProviderInterface<MetricCalculatorInterface>
 */
final class MetricCalculatorCollection implements ServiceProviderInterface, \Countable
{
    /**
     * @param ServiceLocator<MetricCalculatorInterface> $metrics
     */
    public function __construct(
        private readonly ServiceLocator $metrics,
    ) {
    }

    public function count(): int
    {
        return count($this->metrics);
    }

    public function get(string $id): mixed
    {
        return $this->metrics->get($id);
    }

    public function has(string $id): bool
    {
        return $this->metrics->has($id);
    }

    public function getProvidedServices(): array
    {
        return $this->metrics->getProvidedServices();
    }

    /**
     * @return array<int, string>
     */
    public function getMetricsKeys(): array
    {
        return array_keys($this->getProvidedServices());
    }
}
