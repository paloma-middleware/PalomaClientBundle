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
    /** @var  string */
    private $defaultLocale;
    /** @var Paloma[] */
    private $clientCache = [];

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
        if ($this->defaultChannel === null || $this->defaultLocale === null) {
            throw new \LogicException('Attempt to get the default Paloma client without prior ' .
                'defining what the default channel and locale is. Forget to call ' .
                'ClientFactory::setDefaultChannel() or ClientFactory::setDefaultLocale()?');
        }

        return $this->getOrCreateClient($this->defaultChannel, $this->defaultLocale);
    }

    public function getClient($channel, $locale)
    {
        return $this->getOrCreateClient($channel, $locale);
    }

    /**
     * @param $channel string
     * @return Paloma
     */
    private function getOrCreateClient($channel, $locale)
    {
        $key = "$channel-$locale";
        if (isset($this->clientCache[$key])) {
            return $this->clientCache[$key];
        }
        $this->clientCache[$key] = Paloma::create($this->baseUrl, $channel,
            $locale, $this->apiKey, null, null, $this->palomaProfile);
        return $this->clientCache[$key];
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

    /**
     * @return string
     */
    public function getDefaultLocale()
    {
        return $this->defaultLocale;
    }

    /**
     * @param string $defaultLocale
     * @return ClientFactory
     */
    public function setDefaultLocale($defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;
        return $this;
    }

}
