<?php

declare(strict_types=1);

namespace App\Infrastructure\Gitlab\Client\MergeRequest\Query;

use App\Domain\Tenant\Config;
use function parse_url;
use function urlencode;

final class GetDetailsQuery
{
    public function __construct(
        public readonly Config $config,
        public readonly string $projectId,
        public readonly string $mergeRequestIid,
        public readonly string $baseUrl,
    ) {
    }

    public function getHost(): string
    {
        $parsedUrl = parse_url($this->baseUrl);

        return "{$parsedUrl['scheme']}://{$parsedUrl['host']}";
    }

    public function getBaseUrl(): string
    {
        $projectId = urlencode($this->projectId);

        $path = "projects/{$projectId}/merge_requests/{$this->mergeRequestIid}";

        return "{$this->baseUrl}/{$path}";
    }

    public function getDetailsUrl(): string
    {
        return $this->getBaseUrl();
    }

    public function getChangesUrl(): string
    {
        return "{$this->getBaseUrl()}/diffs?per_page=30";
    }

    public function getThreadsUrl(): string
    {
        return "{$this->getBaseUrl()}/discussions";
    }

    public function getCommitsUrl(): string
    {
        return "{$this->getBaseUrl()}/commits";
    }
}
