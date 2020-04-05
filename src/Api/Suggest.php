<?php

namespace Attraqt\Fredhopper\Api;

use Laminas\Http\Client;
use Laminas\Http\Request;

/**
 * Suggest api client for Fredhopper service module
 *
 * @author      Stefan Jaroschek
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2020 Tofex UG (http://www.tofex.de)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Suggest
    extends Api
{
    /** Define scope parameter name */
    const PARAMETER_SCOPE = 'scope';

    /** Define search parameter name */
    const PARAMETER_SEARCH = 'search';

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
        $state,
        $secure = true,
        $user = null,
        $password = null,
        $type = Client::AUTH_BASIC)
    {
        parent::__construct($service, $region, $instance, $state, $secure, $user, $password, $type);

        $this->addAllowedParameter(static::PARAMETER_SCOPE);
        $this->addAllowedParameter(static::PARAMETER_SEARCH);
    }

    /**
     * @param string $scope
     * @param string $search
     *
     * @return Suggest\Result
     * @throws Suggest\Result\Exception
     * @throws Exception
     */
    public function search($scope, $search)
    {
        $client = static::getHttpClient();

        $client->setMethod(Request::METHOD_GET);
        $client->setUri($this->getBaseUri());
        $this->addHeader('Accept-Encoding', 'gzip');
        $this->addParameter(static::PARAMETER_SCOPE, $scope);
        $this->addParameter(static::PARAMETER_SEARCH, $search);

        $response = static::getHttpClient()->send();

        return new Suggest\Result($response);
    }

    /**
     * @return string
     */
    protected function getBaseUri()
    {
        return sprintf('%s://query.%s.%s.%s.%s.fredhopperservices.com/%s/json', $this->secure ? 'https' : 'http',
            $this->state, $this->instance, $this->service, $this->region, $this->user);
    }
}
