<?php


namespace Paloma\ClientBundle\Tests\Factory;


use Cache\Adapter\Void\VoidCachePool;
use Paloma\ClientBundle\Factory\ClientFactory;
use Paloma\Shop\PalomaClientInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ClientFactoryTest extends TestCase
{

    protected function getContainer($config)
    {
        $kernel = new \AppKernel($config, 'test', true);
        $kernel->boot();

        return $kernel->getContainer();
    }

    public function testConfig()
    {
        $container = $this->getContainer('config.yml');

        /** @var ClientFactory $factory */
        $factory = $container->get('paloma_client.client_factory');
        $this->assertEquals('https://palomatest/api', $factory->getBaseUrl());
        $this->assertEquals('TestApiKey', $factory->getApiKey());
        $this->assertInstanceOf(LoggerInterface::class, $factory->getShopClientLogger());
        $this->assertEquals('successlog', $factory->getSuccessLogFormat());
        $this->assertEquals('faillog', $factory->getErrorLogFormat());
        $this->assertInstanceOf(VoidCachePool::class, $factory->getShopClientCache());
    }

    public function testConfigNullLogger()
    {
        $container = $this->getContainer('config_null_logger.yml');

        /** @var ClientFactory $factory */
        $factory = $container->get('paloma_client.client_factory');
        $this->assertEquals('https://palomatest/api', $factory->getBaseUrl());
        $this->assertEquals('TestApiKey', $factory->getApiKey());
        $this->assertInstanceOf(LoggerInterface::class, $factory->getShopClientLogger());
        $this->assertNull($factory->getSuccessLogFormat());
        $this->assertNull($factory->getErrorLogFormat());
        $this->assertNull($factory->getShopClientCache());
    }

    public function testGetDefaultClient()
    {
        $container = $this->getContainer('config.yml');

        /** @var ClientFactory $factory */
        $factory = $container->get('paloma_client.client_factory');
        $factory->setDefaultChannel('default');
        $factory->setDefaultLocale('default');

        $client = $factory->getDefaultClient();
        $this->assertInstanceOf('Paloma\Shop\PalomaClient', $client);

        /** @var PalomaClientInterface $client */
        $client = $container->get('paloma_client.default_client');
        $this->assertInstanceOf('Paloma\ClientBundle\Factory\DefaultPalomaClient', $client);

        $this->assertInstanceOf('Paloma\Shop\Catalog\CatalogClientInterface', $client->catalog());
    }

    public function testGetDefaultClientUninitialized1()
    {
        $container = $this->getContainer('config.yml');

        // Test that a default client can be created even if the ClientFactory
        // is not initialized.
        $client = $container->get('paloma_client.default_client');
        $this->assertInstanceOf('Paloma\ClientBundle\Factory\DefaultPalomaClient', $client);
    }

    public function testGetDefaultClientUninitialized2()
    {
        $container = $this->getContainer('config.yml');

        // Test that we get an error when we try to get a the default
        // PalomaClient directly from the factory when it is not initialized.
        $this->expectException(\LogicException::class);
        $factory = $container->get('paloma_client.client_factory');
        $factory->getDefaultClient();
    }

    public function testGetDefaultClientUninitialized3()
    {
        $container = $this->getContainer('config.yml');

        // Test that a default client can be created even if the ClientFactory
        // is not initialized. However, accessing any properties on the default
        // client has to result in an exception.
        $client = $container->get('paloma_client.default_client');
        $this->assertInstanceOf('Paloma\ClientBundle\Factory\DefaultPalomaClient', $client);
        $this->expectException(\LogicException::class);
        $client->catalog();
    }

}
