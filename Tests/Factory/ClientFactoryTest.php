<?php


namespace Paloma\ClientBundle\Tests\Factory;


use Cache\Adapter\Void\VoidCachePool;
use Paloma\ClientBundle\Factory\ClientFactory;
use Paloma\Shop\Paloma;
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

        /** @var Paloma $client */
        $client = $container->get('paloma_client.default_client');
        $this->assertInstanceOf('Paloma\Shop\PalomaClient', $client);
    }

    public function testGetDefaultFail()
    {
        $container = $this->getContainer('config.yml');

        $this->expectException(\LogicException::class);
        $container->get('paloma_client.default_client');
    }

}
