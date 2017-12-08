<?php

namespace Paloma\ClientBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class PalomaClientExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $def = $container->getDefinition('paloma_client.client_factory');
        $def->replaceArgument(0, $config['base_url']);
        $def->replaceArgument(1, $config['api_key']);
        $def->replaceArgument(4, $config['log_format_success']);
        $def->replaceArgument(5, $config['log_format_failure']);
        $def->replaceArgument(7, $config['cache_provider']);

        $def = $container->getDefinition('paloma_client.paloma_user_auth_listener');
        $def->replaceArgument(0, $config['security']['username_parameter']);
        $def->replaceArgument(1, $config['security']['password_parameter']);
    }
}
