<?php

declare(strict_types=1);

namespace App\Gitlab\Config;

final class ConfigItem
{
    public function __construct(
        public readonly string $name,
        public readonly string $host,
        public readonly string $token
    ) {
    }
}
