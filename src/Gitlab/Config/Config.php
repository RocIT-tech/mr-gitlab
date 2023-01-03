<?php

declare(strict_types=1);

namespace App\Gitlab\Config;

use App\Gitlab\Parser\MergeRequestUrl;
use Exception;
use function parse_url;

final class Config
{
    /**
     * Indexed by host
     *
     * @var array<string, ConfigItem>
     */
    private array $stack = [];

    public function push(ConfigItem $item): void
    {
        $this->stack[$item->host] = $item;
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

    public function getByHost(string $host): ConfigItem
    {
        $config = $this->stack[$host] ?? null;

        if (null === $config) {
            $parsedUrl = parse_url(MergeRequestUrl::fromRaw($host)->baseUrl);
            $host      = "{$parsedUrl['scheme']}://{$parsedUrl['host']}";
            $config    = $this->stack[$host] ?? null;
        }

        return $config ?? throw new Exception("'{$host}' not found in config files.");
    }
}
