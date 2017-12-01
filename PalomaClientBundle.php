<?php

namespace Paloma\ClientBundle;

use Paloma\ClientBundle\DependencyInjection\PalomaClientCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PalomaClientBundle extends Bundle
{

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new PalomaClientCompilerPass());
    }

}
