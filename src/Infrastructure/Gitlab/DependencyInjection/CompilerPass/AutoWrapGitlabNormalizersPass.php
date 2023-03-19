<?php

declare(strict_types=1);

namespace App\Infrastructure\Gitlab\DependencyInjection\CompilerPass;

use App\Infrastructure\Gitlab\Serializer\GitlabNormalizer;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use function array_keys;

final class AutoWrapGitlabNormalizersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        foreach (array_keys($container->findTaggedServiceIds('gitlab.normalizer')) as $gitlabNormalizerId) {
            $container->register("gitlab.{$gitlabNormalizerId}", GitlabNormalizer::class)
                      ->setDecoratedService($gitlabNormalizerId)
                      ->setArguments([new Reference("gitlab.{$gitlabNormalizerId}.inner")]);
        }
    }
}
