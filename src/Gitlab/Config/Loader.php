<?php

declare(strict_types=1);

namespace App\Gitlab\Config;

use Exception;
use Symfony\Component\Finder\Finder;
use function json_decode;
use const JSON_THROW_ON_ERROR;

final class Loader
{
    public function __construct(
        private readonly string $configDirectory,
    ) {
    }

    public function load(): Config
    {
        $configurationFiles = new Finder();
        $configurationFiles
            ->in($this->configDirectory)
            ->name('*.json')
            ->files();

        $config = new Config();

        foreach ($configurationFiles as $configurationFile) {
            if ($configurationFile->isReadable() === false) {
                throw new Exception("`{$configurationFile}` configuration file is not readable.");
            }

            $configurationContent = json_decode(
                $configurationFile->getContents(),
                associative: true,
                flags: JSON_THROW_ON_ERROR,
            );

            $config->push(
                host: $configurationContent['host'],
                name: $configurationContent['name'],
                token: $configurationContent['token'],
            );
        }

        return $config;
    }
}
