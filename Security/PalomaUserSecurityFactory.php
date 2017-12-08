<?php


namespace Paloma\ClientBundle\Security;


use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;

class PalomaUserSecurityFactory implements SecurityFactoryInterface
{

    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $providerId = 'paloma_client.paloma_user_auth_provider' . $id;
        $container->setDefinition($providerId,
            new DefinitionDecorator('paloma_client.paloma_user_auth_provider'));

        $listenerId = 'paloma_client.paloma_user_auth_listener' . $id;
        $container->setDefinition($listenerId,
            new DefinitionDecorator('paloma_client.paloma_user_auth_listener'));

        return [$providerId, $listenerId, $defaultEntryPoint];
    }

    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'paloma_user';
    }

    public function addConfiguration(NodeDefinition $builder)
    {
    }

}
