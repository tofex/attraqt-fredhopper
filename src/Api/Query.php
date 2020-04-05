<?php

namespace Attraqt\Fredhopper\Api;

use Laminas\Http\Client;
use Laminas\Http\Request;
use Tofex\Validator\Xml\Document;

/**
 * Query api client for Fredhopper service module
 *
 * @author      Stefan Jaroschek
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2020 Tofex UG (http://www.tofex.de)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Query
    extends Api
{
    /** Define default sort parameter name */
    const DEFAULT_SORT_PARAMETER = 'fh_sort_by';

    /** @var string */
    protected $schema = '';

    /**
     * @param string $service
     * @param string $region
     * @param string $instance
     * @param string $state
     * @param string $user
     * @param string $password
     * @param string $type
     * @param bool   $secure
     * @param string $schema
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
        $type = Client::AUTH_BASIC,
        $schema = '')
    {
        parent::__construct($service, $region, $instance, $state, $secure, $user, $password, $type);

        $this->schema = $schema;

        $this->addAllowedParameter('fh_session');
        $this->addAllowedParameter('fh_location');
        $this->addAllowedParameter('fh_secondid');
        $this->addAllowedParameter('fh_secondid2');
        $this->addAllowedParameter('fh_view');
        $this->addAllowedParameter('fh_refview');
        $this->addAllowedParameter('fh_reftheme');
        $this->addAllowedParameter('fh_reffacet');
        $this->addAllowedParameter('fh_refsearch');
        $this->addAllowedParameter('fh_oneslice');
        $this->addAllowedParameter('fh_start_index');
        $this->addAllowedParameter('fh_view_size');
        $this->addAllowedParameter('fh_sort_by');
        $this->addAllowedParameter('fh_log');
        $this->addAllowedParameter('fh_allowed_modification');
        $this->addAllowedParameter('fh_disable_redirect');
        $this->addAllowedParameter('fh_facets');
        $this->addAllowedParameter('fh_suppress');
        $this->addAllowedParameter('msort');

        $this->addAllowedParameterPattern('/^fh_maxdisplaynrvalues_.*$/');
    }

    /**
     * @param string $location
     *
     * @return Query\Result
     * @throws Query\Result\Exception
     * @throws Exception
     */
    public function query($location)
    {
        $client = static::getHttpClient();

        $client->setMethod(Request::METHOD_GET);
        $client->setUri($this->getBaseUri());
        $this->addHeader('Accept-Encoding', 'gzip');
        $this->addParameter('fh_location', $location);

        $response = static::getHttpClient()->send();

        $result = new Query\Result($response);

        if ($this->schema) {
            $validator = new Document($this->schema);

            if ( ! $validator->isValid($result->getDocument())) {
                throw new Query\Result\Exception(implode("\n", $validator->getMessages()));
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    protected function getBaseUri()
    {
        return sprintf('%s://query.%s.%s.%s.%s.fredhopperservices.com/fredhopper/query',
            $this->secure ? 'https' : 'http', $this->state, $this->instance, $this->service, $this->region);
    }
}

