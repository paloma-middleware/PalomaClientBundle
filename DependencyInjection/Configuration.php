<?php

namespace Paloma\ClientBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('paloma_client');

        $rootNode
            ->children()
            ->scalarNode('base_url')->isRequired()->end()
            ->scalarNode('api_key')->isRequired()->end()
            ->scalarNode('shop_client_logger')->defaultValue('monolog.logger')->end()
            ->scalarNode('success_log_format')->defaultNull()->end()
            ->scalarNode('error_log_format')->defaultNull()->end()
            ->scalarNode('cache_provider')->defaultNull()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
