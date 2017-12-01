<?php


namespace Paloma\ClientBundle\DependencyInjection;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PalomaClientCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        // Replace some string arguments with their service equivalence
        $clientFactoryDef = $container->getDefinition('paloma_client.client_factory');
        // Logger
        if (($loggerName = $clientFactoryDef->getArgument(2)) !== null) {
            $logger = $container->getDefinition($loggerName);
            $clientFactoryDef->setArgument(2, $logger);
        }
        // Cache provider
        if (($cacheProviderName = $clientFactoryDef->getArgument(6)) !== null) {
            $cacheProvider = $container->getDefinition($cacheProviderName);
            $clientFactoryDef->setArgument(6, $cacheProvider);
        }
    }

}
