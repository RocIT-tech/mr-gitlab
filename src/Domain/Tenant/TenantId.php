<?php

declare(strict_types=1);

namespace App\Domain\Tenant;

use Symfony\Component\Uid\Ulid;

final class TenantId
{
    public function __construct(
        public readonly string $value,
    ) {
        Ulid::fromRfc4122($this->value);
    }
}
