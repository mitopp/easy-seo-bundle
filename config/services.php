<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Mitopp\EasySeoBundle\Domain\Breadcrumb\BreadcrumbManager;
use Mitopp\EasySeoBundle\Domain\Breadcrumb\BreadcrumbManagerInterface;
use Mitopp\EasySeoBundle\Domain\Meta\MetaTagManager;
use Mitopp\EasySeoBundle\Domain\Meta\MetaTagManagerInterface;
use Mitopp\EasySeoBundle\Domain\SeoManager;
use Mitopp\EasySeoBundle\Domain\SeoManagerInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();
    $services
        ->defaults()
        ->public()
        ->autowire()
        ->autoconfigure()
    ;

    $services
        ->load('Mitopp\\EasySeoBundle\\', '../src/*')
        ->exclude('../src/{DependencyInjection,Domain/{SeoManager.php,SeoManagerInterface.php},Domain/Meta/MetaTag.php,Domain/Breadcrumb/Breadcrumb.php,Domain/Config/Config.php,EasySeoBundle.php}')
    ;

    $services
        ->set(SeoManagerInterface::class, SeoManager::class)
        ->args([
            service(BreadcrumbManagerInterface::class),
            service(MetaTagManagerInterface::class),
            '%seo.site_title%',
            '%seo.separator%',
            '%seo.append_site_title%',
            '%seo.meta_tags%',
        ])
    ;

    $services->alias(SeoManager::class, SeoManagerInterface::class);

    $services->alias(MetaTagManagerInterface::class, MetaTagManager::class);
    $services->alias(BreadcrumbManagerInterface::class, BreadcrumbManager::class);
};
