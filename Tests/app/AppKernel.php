<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{

    /** @var  string */
    private $config;

    public function __construct($config, $environment, $debug)
    {
        parent::__construct($environment, $debug);

        $this->config = $config;
    }

    public function registerBundles()
    {
        return array(
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\MonologBundle\MonologBundle(),
            new \Paloma\ClientBundle\PalomaClientBundle(),
        );
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__. '/' . $this->config);
    }

    public function getCacheDir()
    {
        $baseCacheDir = parent::getCacheDir();
        return $baseCacheDir . '/' . $this->config;
    }

}
