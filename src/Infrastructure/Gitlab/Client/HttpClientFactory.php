<?php

declare(strict_types=1);

namespace App\Infrastructure\Gitlab\Client;

use App\Domain\Tenant\Config;
use Symfony\Component\HttpClient\HttpClientTrait;
use Symfony\Component\HttpClient\ScopingHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class HttpClientFactory
{
    use HttpClientTrait;

    public function __construct(
        private readonly HttpClientInterface $gitlabClient,
    ) {
    }

    public function create(Config $config): HttpClientInterface
    {
        return ScopingHttpClient::forBaseUri($this->gitlabClient, $config->host, [
            'headers' => [
                'PRIVATE-TOKEN' => $config->token,
            ],
        ]);
    }
}
