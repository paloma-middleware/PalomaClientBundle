<?php


namespace Paloma\ClientBundle\Factory;


use Paloma\Shop\PalomaClientInterface;

/**
 * A Paloma client implementation which delegates all calls to the default
 * Paloma client as defined in the ClientFactory.
 *
 * The reason for this delegator to exist instead of just using a plain
 * PalomClient instance is that with Symfony auto configuration services are
 * instantiated at a very early stage in the application startup and it can
 * very well be before a default client is configured in the ClientFactory.
 * This delegator ensures that the default Paloma client always uses the latest
 * definition of the default client at all times.
 */
class DefaultPalomaClient implements PalomaClientInterface
{

    /** @var ClientFactory */
    private $clientFactory;

    public function __construct(ClientFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }

    public function catalog()
    {
        return $this->clientFactory->getDefaultClient()->catalog();
    }

    public function checkout()
    {
        return $this->clientFactory->getDefaultClient()->checkout();
    }

    public function customers()
    {
        return $this->clientFactory->getDefaultClient()->customers();
    }

}
