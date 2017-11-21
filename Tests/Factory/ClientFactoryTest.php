<?php


namespace Paloma\ClientBundle\Tests\Factory;


use Paloma\ClientBundle\Factory\ClientFactory;
use Paloma\Shop\Paloma;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ClientFactoryTest extends TestCase
{

    /** @var  ContainerInterface */
    private $container;

    protected function setUp()
    {
        $kernel = new \AppKernel('test', true);
        $kernel->boot();

        $this->container = $kernel->getContainer();
    }

    public function testConfig()
    {
        /** @var ClientFactory $factory */
        $factory = $this->container->get('paloma_client.client_factory');
        $this->assertEquals('https://palomatest/api', $factory->getBaseUrl());
        $this->assertEquals('TestApiKey', $factory->getApiKey());
    }

    public function testGetDefaultClient()
    {
        /** @var ClientFactory $factory */
        $factory = $this->container->get('paloma_client.client_factory');
        $factory->setDefaultChannel('default');

        $client = $factory->getDefaultClient();
        $this->assertInstanceOf('Paloma\Shop\Paloma', $client);

        /** @var Paloma $client */
        $client = $this->container->get('paloma_client.default_client');
        $this->assertInstanceOf('Paloma\Shop\Paloma', $client);
    }

    public  function testGetDefaultFail()
    {
        $this->expectException(\LogicException::class);
        $this->container->get('paloma_client.default_client');
    }

}
