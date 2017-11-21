<?php


namespace Paloma\ClientBundle\Factory;


use Paloma\Shop\Paloma;
use Paloma\Shop\PalomaProfiler;

class ClientFactory
{
    /** @var  string */
    private $baseUrl;
    /** @var  string */
    private $apiKey;
    /** @var  PalomaProfiler */
    private $palomaProfile;
    /** @var  string */
    private $defaultChannel;
    /** @var Paloma[] */
    private $clientByChannel = [];

    public function __construct($baseUrl, $apiKey, PalomaProfiler $palomaProfile = null)
    {
        $this->baseUrl = $baseUrl;
        $this->apiKey = $apiKey;
        $this->palomaProfile = $palomaProfile;
    }

    /**
     * @return Paloma
     */
    public function getDefaultClient()
    {
        if ($this->defaultChannel === null) {
            throw new \LogicException('Attempt to get the default Paloma client without prior ' .
                'defining what the default channel is. Forget to call ClientFactory::setDefaultChannel()?');
        }

        return $this->getOrCreateClient($this->defaultChannel);
    }

    public function getClient($channel)
    {
        return $this->getOrCreateClient($channel);
    }

    /**
     * @param $channel string
     * @return Paloma
     */
    private function getOrCreateClient($channel)
    {
        if (isset($this->clientByChannel[$channel])) {
            return $this->clientByChannel[$channel];
        }
        $this->clientByChannel[$channel] = Paloma::create($this->baseUrl, $channel,
            $this->apiKey, null, null, $this->palomaProfile);
        return $this->clientByChannel[$channel];
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @return string
     */
    public function getDefaultChannel()
    {
        return $this->defaultChannel;
    }

    /**
     * @param string $defaultChannel
     * @return ClientFactory
     */
    public function setDefaultChannel($defaultChannel)
    {
        $this->defaultChannel = $defaultChannel;
        return $this;
    }

}
