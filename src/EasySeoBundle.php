<?php

declare(strict_types=1);

namespace Mitopp\EasySeoBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class EasySeoBundle extends AbstractBundle
{
    protected string $extensionAlias = 'mitopp_easy_seo';

    /**
     * @param array<string, mixed> $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.php');

        /** @var array<string, mixed> $title */
        $title = $config['title'] ?? [];

        /** @var array<string, string> $metaTags */
        $metaTags = $config['meta_tags'] ?? [];

        $parameters = $container->parameters();
        $parameters
            ->set('seo.site_title', $title['site'] ?? '')
            ->set('seo.separator', $title['separator'] ?? '-')
            ->set('seo.append_site_title', $title['append_site_title'] ?? true)
            ->set('seo.meta_tags', $metaTags)
        ;
    }

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->info('Easy SEO configuration.')
            ->children()
                ->arrayNode('title')
                    ->children()
                        ->stringNode('site')
                            ->defaultValue('')
                            ->info('The title of the whole site.')
                            ->example('My Site')
                            ->cannotBeEmpty()
                        ->end() // site
                        ->stringNode('separator')
                            ->defaultValue('-')
                            ->info('The separator of the page title and site title.')
                        ->end() // separator
                        ->booleanNode('append_site_title')
                            ->defaultValue(true)
                            ->info('Whether to append the site title to the page title.')
                        ->end() // append_site_title
                    ->end() // title
                ->end()
            ->end()
            ->children()
                ->arrayNode('meta_tags')
                    ->info('Meta tags for the whole site.')
                    ->ignoreExtraKeys(false)
                    ->normalizeKeys(false)
                    ->useAttributeAsKey('name')
                    ->scalarPrototype()->end()
                ->end()
            ->end()
        ;
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
