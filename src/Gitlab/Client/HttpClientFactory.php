<?php

declare(strict_types=1);

namespace App\Gitlab\Client;

use App\Gitlab\Config\Config;
use App\Gitlab\Config\ConfigContext;
use Symfony\Component\HttpClient\HttpClientTrait;
use Symfony\Component\HttpClient\ScopingHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function implode;
use function preg_quote;

final class HttpClientFactory
{
    use HttpClientTrait;

    public function __construct(
        private readonly Config        $config,
        private readonly ConfigContext $configContext,
    ) {
    }

    public function create(HttpClientInterface $client): HttpClientInterface
    {
        $config = $this->config->getByHost($this->configContext->getHost());

        $regexp = preg_quote(implode('', self::resolveUrl(self::parseUrl('.'), self::parseUrl($config->host))), null);

        return new ScopingHttpClient(
            $client,
            [
                $regexp => [
                    'base_uri' => $config->host,
                    'headers'  => [
                        'PRIVATE-TOKEN' => $config->token,
                    ],
                ],
            ]
        );
    }
}
