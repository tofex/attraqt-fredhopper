<?php

namespace Attraqt\Fredhopper\Api\Service\Result;

use Attraqt\Fredhopper\Api\Service\Result;
use Laminas\Http\Response;

/**
 * Service api client response for Fredhopper service module
 *
 * @author      Stefan Jaroschek
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2020 Tofex UG (http://www.tofex.de)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Trigger
    extends Result
{
    /** @var string */
    protected $dataId;

    /**
     * @param Response $response
     * @param string   $dataId
     *
     * @throws Trigger\Exception
     * @throws \Attraqt\Fredhopper\Api\Service\Exception
     */
    public function __construct(Response $response, $dataId = null)
    {
        parent::__construct($response);

        if ($dataId) {
            $this->dataId = $dataId;
        } else {
            $matches = array();

            if ( ! (preg_match(static::DATA_ID_REGEX, $response->getHeaders()->get('Location'), $matches))) {
                throw new Trigger\Exception('No data-id for trigger found in response headers');
            }

            $this->dataId = $matches[ 0 ];
        }
    }

    /**
     * @return string
     */
    public function getDataId()
    {
        return $this->dataId;
    }
}

