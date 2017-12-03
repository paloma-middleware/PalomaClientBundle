<?php


namespace Paloma\ClientBundle\DependencyInjection;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class PalomaClientCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        // Replace some string arguments with their service equivalents
        $clientFactoryDef = $container->getDefinition('paloma_client.client_factory');
        // Cache provider
        $this->replaceReferenceArgument($container, $clientFactoryDef, 7);
    }

    /**
     * This method helps injecting services which have not yet been created into
     * container references (services which are not yet created). It does this
     * by replacing the specified constructor argument which has to contain a
     * string denoting a container service with the actual reference to that
     * service.
     * It does so in a Symfony 2.x compatible way.
     *
     * @param ContainerBuilder $container
     * @param Definition $reference
     * @param int $argIndex
     */
    private function replaceReferenceArgument(ContainerBuilder $container,
        Definition $reference, $argIndex)
    {
        $arguments = $reference->getArguments();
        if ($arguments[$argIndex] !== null) {
            $replacement = $container->getDefinition($arguments[$argIndex]);
            $arguments[$argIndex] = $replacement;
            $reference->setArguments($arguments);
        }
    }

}
