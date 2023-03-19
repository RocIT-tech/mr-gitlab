<?php

declare(strict_types=1);

namespace App;

use App\Infrastructure\Gitlab\DependencyInjection\CompilerPass\AutoWrapGitlabNormalizersPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import('../config/{packages}/*.yaml');
        $container->import('../config/{packages}/' . $this->environment . '/*.yaml');

        $container->import('../config/services.yaml');
        $container->import('../config/{services}/**/*.yaml');
        $container->import('../config/{services}_' . $this->environment . '.yaml');
    }

    protected function getContainerBuilder(): ContainerBuilder
    {
        $container = parent::getContainerBuilder();

        $container->addCompilerPass(new AutoWrapGitlabNormalizersPass());

        return $container;
    }
}
