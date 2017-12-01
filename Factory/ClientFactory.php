<?php


namespace Paloma\ClientBundle\Factory;


use Paloma\Shop\Paloma;
use Paloma\Shop\PalomaProfiler;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ClientFactory
{
    /** @var  string */
    private $baseUrl;
    /** @var  string */
    private $apiKey;
    /** @var  SessionInterface */
    private $session;
    /** @var  LoggerInterface */
    private $shopClientLogger;
    /** @var  string */
    private $successLogFormat;
    /** @var  string */
    private $errorLogFormat;
    /** @var  CacheItemPoolInterface */
    private $shopClientCache;
    /** @var  PalomaProfiler */
    private $palomaProfiler;
    /** @var  string */
    private $defaultChannel;
    /** @var  string */
    private $defaultLocale;
    /** @var Paloma[] */
    private $clientCache = [];

    public function __construct($baseUrl, $apiKey, SessionInterface $session = null,
        LoggerInterface $shopClientLogger = null, $successLogFormat = null,
        $errorLogFormat = null, PalomaProfiler $palomaProfile = null,
        CacheItemPoolInterface $shopClientCache = null)
    {
        $this->baseUrl = $baseUrl;
        $this->apiKey = $apiKey;
        $this->session = $session;
        $this->shopClientLogger = $shopClientLogger;
        $this->successLogFormat = $successLogFormat;
        $this->errorLogFormat = $errorLogFormat;
        $this->palomaProfiler = $palomaProfile;
        $this->shopClientCache = $shopClientCache;
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

    /**
     * @param $channel string The Paloma channel to use
     * @param $locale string The Paloma locale to use
     * @return Paloma
     */
    public function getClient($channel, $locale)
    {
        return $this->getOrCreateClient($channel, $locale);
    }

    /**
     * @param $channel string
     * @param $locale string
     * @return Paloma
     */
    private function getOrCreateClient($channel, $locale)
    {
        $key = "$channel-$locale";
        if (isset($this->clientCache[$key])) {
            return $this->clientCache[$key];
        }
        $this->clientCache[$key] = Paloma::create($this->baseUrl, $this->apiKey,
            $channel, $locale, $this->session, $this->shopClientLogger, $this->successLogFormat,
            $this->errorLogFormat, $this->palomaProfiler, $this->shopClientCache);
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

    /**
     * @return LoggerInterface
     */
    public function getShopClientLogger()
    {
        return $this->shopClientLogger;
    }

    /**
     * @return string
     */
    public function getSuccessLogFormat()
    {
        return $this->successLogFormat;
    }

    /**
     * @return string
     */
    public function getErrorLogFormat()
    {
        return $this->errorLogFormat;
    }

    /**
     * @return PalomaProfiler
     */
    public function getPalomaProfiler()
    {
        return $this->palomaProfiler;
    }

    /**
     * @return CacheItemPoolInterface
     */
    public function getShopClientCache()
    {
        return $this->shopClientCache;
    }

}
