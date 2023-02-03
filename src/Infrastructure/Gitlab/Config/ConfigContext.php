<?php

declare(strict_types=1);

namespace App\Infrastructure\Gitlab\Config;

final class ConfigContext
{
    public function __construct(
        public readonly string $host,
    ) {
    }
}
