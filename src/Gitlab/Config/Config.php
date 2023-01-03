<?php

declare(strict_types=1);

namespace App\Gitlab\Config;

use Exception;

final class Config
{
    /**
     * Indexed by host
     *
     * @var array<string, ConfigItem>
     */
    private array $stack = [];

    public function push(string $host, string $name, string $token): void
    {
        $this->stack[$host] = new ConfigItem(
            $name,
            $host,
            $token
        );
    }

    /**
     * Indexed by host
     *
     * @return array<string, ConfigItem>
     */
    public function all(): array
    {
        return $this->stack;
    }
}
