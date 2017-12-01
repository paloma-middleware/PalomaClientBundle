<?php


namespace Paloma\ClientBundle\DependencyInjection;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PalomaClientCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        // Replace the string argument with the name of the logger to use in
        // paloma_client.client_factory with an acutal service reference.
        $clientFactoryDef = $container->getDefinition('paloma_client.client_factory');
        if (($loggerName = $clientFactoryDef->getArgument(2)) !== null) {
            $logger = $container->getDefinition($loggerName);
            $clientFactoryDef->setArgument(2, $logger);
        }
    }

}
