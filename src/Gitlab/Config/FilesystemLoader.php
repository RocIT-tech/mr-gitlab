<?php

declare(strict_types=1);

namespace App\Gitlab\Config;

use App\Metrics\Metric;
use App\Metrics\MetricCalculatorInterface;
use Exception;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\Finder\Finder;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\ExpressionSyntax;
use Symfony\Component\Validator\Constraints\Sequentially;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Validation;
use function json_decode;
use const JSON_THROW_ON_ERROR;

final class FilesystemLoader
{
    /**
     * @param ServiceLocator<MetricCalculatorInterface> $metrics
     */
    public function __construct(
        private readonly string         $configDirectory,
        private readonly ServiceLocator $metrics,
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

        $resolver = new OptionsResolver();

        foreach (Metric::cases() as $metric) {
            $resolver
                ->setDefault($metric->value, function (OptionsResolver $metricResolver) use ($metric) {
                    $metricResolver
                        ->setDefaults([
                            'enabled'    => true,
                            'constraint' => $this->metrics->get($metric->value)->getDefaultConstraint(),
                        ])
                        ->setAllowedTypes('enabled', 'bool')
                        ->setAllowedValues('constraint', Validation::createIsValidCallable(
                            new Sequentially([
                                new Type('string'),
                                new ExpressionSyntax(
                                    allowedVariables: ['value']
                                ),
                            ]),
                        ));
                });
        }

        foreach ($configurationFiles as $configurationFile) {
            if ($configurationFile->isReadable() === false) {
                throw new Exception("`{$configurationFile}` configuration file is not readable.");
            }

            /** @var array{name: string, host: string, token: string, metrics?: array<string, array{enabled?: bool}>} $configurationContent */
            $configurationContent = json_decode(
                $configurationFile->getContents(),
                associative: true,
                flags: JSON_THROW_ON_ERROR,
            );

            $config->push(new ConfigItem(
                name: $configurationContent['name'],
                host: $configurationContent['host'],
                token: $configurationContent['token'],
                configMetrics: new ConfigItemMetrics($resolver->resolve($configurationContent['metrics'] ?? []))
            ));
        }

        return $config;
    }
}
