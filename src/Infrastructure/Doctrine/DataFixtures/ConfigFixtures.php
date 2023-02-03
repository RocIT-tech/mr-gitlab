<?php

namespace App\Infrastructure\Doctrine\DataFixtures;

use App\Domain\Metrics\Metric;
use App\Infrastructure\Doctrine\Entity\Config as ConfigEntity;
use App\Infrastructure\Doctrine\Entity\ConfigMetric as ConfigMetricEntity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\ExpressionSyntax;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Sequentially;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Validation;
use function json_decode;
use function trim;

class ConfigFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly ClockInterface $clock,
        private readonly string         $configDirectory,
    ) {
    }

    public function getDependencies(): array
    {
        return [
            AccountFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadFromLocalFiles($manager);

        $manager->flush();
    }

    private function loadFromLocalFiles(ObjectManager $manager): void
    {
        $configurationFiles = new Finder();
        $configurationFiles
            ->in($this->configDirectory)
            ->name('*.json')
            ->files();

        $fields = [];

        foreach (Metric::cases() as $metric) {
            $fields[$metric->value] = new Type('array');
        }

        $resolver = new OptionsResolver();
        $resolver
            ->setRequired(['name', 'host', 'token'])
            ->setAllowedTypes('name', 'string')
            ->setAllowedTypes('host', 'string')
            ->setAllowedTypes('token', 'string')
            ->setDefault('metrics', function (OptionsResolver $metricsResolver): void {
                $metricsResolver
                    ->setPrototype(true)
                    ->setDefault('enabled', function (Options $options): bool {
                        $constraint = $options['constraint'] ?? null;

                        return null !== $constraint && '' !== trim($constraint);
                    })
                    ->setAllowedTypes('enabled', 'bool')
                    ->setDefault('constraint', null)
                    ->setAllowedValues('constraint', Validation::createIsValidCallable(
                        new Sequentially([
                            new Type('string'),
                            new Length(min: 9),
                            new ExpressionSyntax(
                                allowedVariables: ['value']
                            ),
                        ]),
                    ));
            })
            ->setAllowedValues('metrics', Validation::createIsValidCallable(
                new Collection(
                    fields: $fields,
                    allowExtraFields: false,
                    allowMissingFields: true,
                ),
            ));

        foreach ($configurationFiles as $configurationFile) {
            if ($configurationFile->isReadable() === false) {
                throw new Exception("`{$configurationFile}` configuration file is not readable.");
            }

            /** @var array{
             *     name: string,
             *     host: string,
             *     token: string,
             *     metrics?: array<string, array{
             *      enabled?: bool,
             *      constraint?: string
             *     }>} $configurationContent
             */
            $configurationContent = json_decode(
                $configurationFile->getContents(),
                associative: true,
                flags: JSON_THROW_ON_ERROR,
            );

            $resolvedConfig = $resolver->resolve($configurationContent);

            $configEntity = new ConfigEntity(
                id: (new Ulid(Ulid::generate($this->clock->now())))->toRfc4122(),
                host: $resolvedConfig['host'],
                name: $resolvedConfig['name'],
                token: $resolvedConfig['token']
            );
            $configEntity->attachAccount($this->getReference('account'));
            $manager->persist($configEntity);

            foreach ($resolvedConfig['metrics'] as $metricName => $metricConfig) {
                if (true === $metricConfig['enabled'] && null === $metricConfig['constraint']) {
                    continue;
                }

                $configMetricEntity = new ConfigMetricEntity(
                    id: (new Ulid(Ulid::generate($this->clock->now())))->toRfc4122(),
                    key: Metric::from($metricName),
                    enabled: $metricConfig['enabled'],
                    assert: $metricConfig['constraint'],
                );
                $configEntity->addConfigMetric($configMetricEntity);
                $manager->persist($configMetricEntity);
            }

            $this->addReference("config.{$configEntity->host}", $configEntity);
        }
    }
}
