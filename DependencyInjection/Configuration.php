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
                ->scalarNode('log_format_success')->defaultNull()->end()
                ->scalarNode('log_format_failure')->defaultNull()->end()
                ->scalarNode('cache_provider')->defaultNull()->end()
                ->arrayNode('security')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('username_parameter')->defaultValue('_paloma_client_user_username')->end()
                        ->scalarNode('password_parameter')->defaultValue('_paloma_client_user_password')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
