<?php

declare(strict_types=1);

namespace App\Gitlab\Config;

final class ConfigContext
{
    public function __construct(private string $host = '')
    {
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function withHost(string $host): void
    {
        $this->host = $host;
    }
}
