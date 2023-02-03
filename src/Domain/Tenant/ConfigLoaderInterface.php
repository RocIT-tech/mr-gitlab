<?php

declare(strict_types=1);

namespace App\Domain\Tenant;

interface ConfigLoaderInterface
{
    public function load(TenantId $tenantId, string $host): Config;
}
