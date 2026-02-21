<?php

declare(strict_types=1);

namespace Mitopp\EasySeoBundle\Tests\Common;

use Mitopp\EasySeoBundle\EasySeoBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
        yield new EasySeoBundle();
    }

    private function configureContainer(ContainerConfigurator $container, LoaderInterface $loader, ContainerBuilder $builder): void
    {
        $container->extension('framework', [
            'test' => true,
        ]);

        $container->extension('mitopp_easy_seo', [
            'title' => [
                'site' => 'Test Site',
                'separator' => '|',
                'append_site_title' => true,
            ],
        ]);

        $builder->setParameter('kernel.secret', 'test');
    }
}
