<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine;

use App\Domain\Metrics\MetricCalculatorCollection;
use App\Domain\Tenant\Config;
use App\Domain\Tenant\ConfigLoaderInterface;
use App\Domain\Tenant\ConfigMetrics;
use App\Domain\Tenant\TenantId;
use App\Infrastructure\Doctrine\Entity\Config as ConfigEntity;
use Doctrine\Persistence\ManagerRegistry;

final class ConfigLoader implements ConfigLoaderInterface
{
    public function __construct(
        private readonly MetricCalculatorCollection $metrics,
        private readonly ManagerRegistry            $registry,
    ) {
    }

    public function load(TenantId $tenantId, string $host): Config
    {
        $metricsRepository = $this->registry->getRepository(ConfigEntity::class);

        $qb = $metricsRepository->createQueryBuilder('config');

        $qb
            ->addSelect('configMetrics')
            ->innerJoin('config.configMetrics', 'configMetrics')
            ->where($qb->expr()->eq('config.account', ':tenantId'))
            ->setParameter('tenantId', $tenantId->value);

        /** @var ConfigEntity $persistedConfig */
        $persistedConfig = $qb->getQuery()->getSingleResult();

        $allMetrics = $this->metrics->getMetricsKeys();

        $configMetrics = [];

        foreach ($allMetrics as $key) {
            $configMetrics[$key] = [
                'enabled'    => true,
                'constraint' => $this->metrics->get($key)->getDefaultConstraint(),
            ];
        }

        foreach ($persistedConfig->getConfigMetrics() as $persistedMetric) {
            $configMetrics[$persistedMetric->key->value] = [
                'enabled'    => $persistedMetric->enabled,
                'constraint' => $persistedMetric->assert ?? $this->metrics->get($persistedMetric->key->value)->getDefaultConstraint(),
            ];
        }

        return new Config(
            name: $persistedConfig->name,
            host: $persistedConfig->host,
            token: $persistedConfig->token,
            configMetrics: new ConfigMetrics($configMetrics)
        );
    }
}
