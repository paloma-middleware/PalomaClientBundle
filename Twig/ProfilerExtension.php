<?php


namespace Paloma\ClientBundle\Twig;


use Twig\Loader\FilesystemLoader;

class ProfilerExtension extends \Twig_Extension
{

    public function __construct(FilesystemLoader $loader, $kernelRootDir)
    {
        $loader->addPath($kernelRootDir . '/../vendor/paloma/shop-client/templates',
            'paloma_shop_client');
    }

}
