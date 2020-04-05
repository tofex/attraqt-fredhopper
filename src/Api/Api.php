<?php

namespace Attraqt\Fredhopper\Api;

use Laminas\Http\Client;
use Laminas\Http\Client\Adapter\Curl;

/**
 * Abstract api client for Fredhopper service module
 *
 * @author      Stefan Jaroschek
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2020 Tofex UG (http://www.tofex.de)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
abstract class Api
{
    /** Define possible services */
    const FAS = 'fas';
    const SUGGEST = 'suggest';

    /** Define possible regions */
    const AP1 = 'ap1';
    const EU1 = 'eu1';
    const US1 = 'us1';

    /** Define possible service instances */
    const LIVE1 = 'live1';
    const TEST1 = 'test1';
    const TEST2 = 'test2';
    const TEST3 = 'test3';
    const TEST4 = 'test4';

    /** Define possible configuration states */
    const PUBLISHED = 'published';
    const PREPUBLISHED = 'prepublished';

    /** @var Client */
    private static $httpClient;

    /** @var string */
    protected $service;

    /** @var string */
    protected $region;

    /** @var string */
    protected $instance;

    /** @var string */
    protected $state;

    /** @var bool */
    protected $secure;

    /** @var string */
    protected $user;

    /** @var string */
    protected $password;

    /** @var array */
    private $allowedParameters = array();

    /** @var array */
    private $allowedParameterPatterns = array();

    /**
     * @param string $service
     * @param string $region
     * @param string $instance
     * @param string $state
     * @param bool   $secure
     * @param string $user
     * @param string $password
     * @param string $type
     *
     * @throws Exception
     */
    public function __construct(
        $service,
        $region,
        $instance,
        $state = null,
        $secure = true,
        $user = null,
        $password = null,
        $type = Client::AUTH_BASIC)
    {
        if ( ! in_array($service, array(static::FAS, static::SUGGEST))) {
            throw new Exception(sprintf('Invalid service: %s', $service));
        }

        $this->service = $service;

        if ( ! in_array($region, array(static::AP1, static::EU1, static::US1))) {
            throw new Exception(sprintf('Invalid region: %s', $region));
        }

        $this->region = $region;

        if ( ! in_array($instance, array(static::LIVE1, static::TEST1, static::TEST2, static::TEST3, static::TEST4))) {
            throw new Exception(sprintf('Invalid service instance: %s', $instance));
        }

        $this->instance = $instance;

        if ( ! is_null($state)) {
            if ( ! in_array($state, array(static::PUBLISHED, static::PREPUBLISHED))) {
                throw new Exception(sprintf('Invalid configuration state: %s', $state));
            }

            $this->state = $state;
        }

        if ( ! is_bool($secure)) {
            throw new Query\Exception(sprintf('Invalid secure flag %s', $secure));
        }

        $this->secure = $secure;
        $this->user = $user;
        $this->password = $password;

        $adapter = new Curl();

        $adapter->setCurlOption(CURLOPT_DNS_USE_GLOBAL_CACHE, false);

        $client = static::getHttpClient();

        $client->setAdapter($adapter);

        if ($user && $password && $type) {
            $client->setAuth($user, $password, $type);
        }
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function addHeader($key, $value)
    {
        static::getHttpClient()->getRequest()->getHeaders()->addHeaderLine($key, $value);
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @throws Exception
     */
    public function addParameter($key, $value)
    {
        if ( ! in_array($key, $this->allowedParameters)) {
            $found = false;

            foreach ($this->allowedParameterPatterns as $pattern) {
                if (preg_match($pattern, $key)) {
                    $found = true;
                    break;
                }
            }

            if ( ! $found) {
                $message = sprintf('Invalid parameter: %s', $key);

                throw new Exception($message);
            }
        }

        static::getHttpClient()->getRequest()->getQuery()->set($key, $value);
    }

    /**
     * Gets the HTTP client object.
     *
     * @return Client
     */
    final public static function getHttpClient()
    {
        if ( ! static::$httpClient instanceof Client) {
            static::$httpClient = new Client();
        }

        return static::$httpClient;
    }

    /**
     * @return array
     */
    protected function getAllowedParameters()
    {
        return $this->allowedParameters;
    }

    /**
     * @param array $allowedParameters
     */
    protected function setAllowedParameters(array $allowedParameters)
    {
        $this->allowedParameters = $allowedParameters;
    }

    /**
     * @param string $allowedParameter
     */
    protected function addAllowedParameter($allowedParameter)
    {
        $this->allowedParameters[] = $allowedParameter;
    }

    /**
     * @return array
     */
    protected function getAllowedParameterPatterns()
    {
        return $this->allowedParameterPatterns;
    }

    /**
     * @param array $allowedParameterPatterns
     */
    protected function setAllowedParameterPatterns(array $allowedParameterPatterns)
    {
        $this->allowedParameterPatterns = $allowedParameterPatterns;
    }

    /**
     * @param string $allowedParameterPattern
     */
    protected function addAllowedParameterPattern($allowedParameterPattern)
    {
        $this->allowedParameterPatterns[] = $allowedParameterPattern;
    }
}
