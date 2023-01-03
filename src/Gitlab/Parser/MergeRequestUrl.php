<?php

declare(strict_types=1);

namespace App\Gitlab\Parser;

use function array_key_exists;
use function parse_url;

final class MergeRequestUrl
{
    public function __construct(
        public readonly string $projectId,
        public readonly string $mergeRequestIid,
        public readonly string $baseUrl,
    ) {
    }

    public function getBaseApiV4Url(): string
    {
        return "{$this->baseUrl}/api/v4";
    }

    public static function fromRaw(string $url): self
    {
        $parsedUrl = parse_url($url);

        $baseUrlPort = ':';
        if (array_key_exists('port', $parsedUrl) === true) {
            $baseUrlPort .= $parsedUrl['port'];
        } else {
            $baseUrlPort .= 'https' === $parsedUrl['scheme'] ? '443' : '80';
        }
        $baseUrl = "{$parsedUrl['scheme']}://{$parsedUrl['host']}{$baseUrlPort}";

        $matches = null;
        preg_match(
            '#^/(?<projectId>.*)/-/merge_requests/(?<mergeRequestIid>[^/]*)/?.*$#',
            $parsedUrl['path'],
            $matches,
        );

        return new self(
            $matches['projectId'],
            $matches['mergeRequestIid'],
            $baseUrl,
        );
    }
}
