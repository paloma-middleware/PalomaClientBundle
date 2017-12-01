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
        if (($loggerName = $clientFactoryDef->getArgument(3)) !== null) {
            $logger = $container->getDefinition($loggerName);
            $clientFactoryDef->setArgument(3, $logger);
        }
        // Cache provider
        if (($cacheProviderName = $clientFactoryDef->getArgument(7)) !== null) {
            $cacheProvider = $container->getDefinition($cacheProviderName);
            $clientFactoryDef->setArgument(7, $cacheProvider);
        }
    }

}
