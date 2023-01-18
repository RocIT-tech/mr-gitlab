<?php

declare(strict_types=1);

namespace App\Gitlab\Client;

use App\Gitlab\Config\Config;
use Symfony\Component\HttpClient\HttpClientTrait;
use Symfony\Component\HttpClient\ScopingHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function implode;
use function preg_quote;

final class HttpClientFactory
{
    use HttpClientTrait;

    public function __construct(
        private readonly Config $config,
    ) {
    }

    public function create(HttpClientInterface $client): HttpClientInterface
    {
        $defaultOptionsByRegexp = [];

        foreach ($this->config->all() as $host => $config) {
            $regexp = preg_quote(implode('', self::resolveUrl(self::parseUrl('.'), self::parseUrl($host))), null);

            $defaultOptionsByRegexp[$regexp] = [
                'base_uri' => $host,
                'headers'  => [
                    'PRIVATE-TOKEN' => $config->token,
                ],
            ];
        }

        return new ScopingHttpClient(
            $client,
            $defaultOptionsByRegexp
        );
    }
}
