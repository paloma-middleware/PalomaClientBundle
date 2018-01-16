<?php


namespace Paloma\ClientBundle\Factory;


use Paloma\Shop\Paloma;
use Paloma\Shop\PalomaClient;
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
    /** @var PalomaClient[] */
    private $clientCache = [];
    /** @var string */
    private $palomaTraceId = null;

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
     * @return PalomaClient
     */
    public function getDefaultClient()
    {
        // There are certain situations in which an even correctly implemented
        // user of the ClientBundle would run into the situation that a default
        // Paloma client is requested without the factory being intitalized
        // properly. This mostly happens due to container wiring during cache
        // warmup or within Twig extensions.
        // Ideally we would throw an exception here but this would make the life
        // of the bundle user much more inconvenient, instead we ensure that any
        // call to Paloma will fail for sure.
        if ($this->defaultChannel === null || $this->defaultLocale === null) {
            return $this->getOrCreateClient('uninitialized_paloma_client_channel',
                'uninitialized_paloma_client_locale');
        }

        return $this->getOrCreateClient($this->defaultChannel, $this->defaultLocale);
    }

    /**
     * @param $channel string The Paloma channel to use
     * @param $locale string The Paloma locale to use
     * @return PalomaClient
     */
    public function getClient($channel, $locale)
    {
        return $this->getOrCreateClient($channel, $locale);
    }

    /**
     * @param $channel string
     * @param $locale string
     * @return PalomaClient
     */
    private function getOrCreateClient($channel, $locale)
    {
        $key = "$channel-$locale";
        if (isset($this->clientCache[$key])) {
            return $this->clientCache[$key];
        }
        $this->clientCache[$key] = Paloma::create([
            'base_url' => $this->baseUrl,
            'api_key' => $this->apiKey,
            'channel' => $channel,
            'locale' => $locale,
            'session' => $this->session,
            'logger' => $this->shopClientLogger,
            'log_format_success' => $this->successLogFormat,
            'log_format_failure' => $this->errorLogFormat,
            'profiler' => $this->palomaProfiler,
            'cache' => $this->shopClientCache,
            'trace_id' => $this->palomaTraceId,
        ]);
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

    /**
     * @return string
     */
    public function getPalomaTraceId()
    {
        return $this->palomaTraceId;
    }

    /**
     * @param string $palomaTraceId
     * @return ClientFactory
     */
    public function setPalomaTraceId($palomaTraceId)
    {
        $this->palomaTraceId = $palomaTraceId;
        return $this;
    }

}
