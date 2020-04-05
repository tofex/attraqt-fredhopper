<?php

namespace Attraqt\Fredhopper\Api\Query;

use DOMDocument;
use Laminas\Http\Response;
use Laminas\Xml\Security;

/**
 * Query api response for Fredhopper service module
 *
 * @author      Stefan Jaroschek
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2020 Tofex UG (http://www.tofex.de)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Result
{
    /** @var Response */
    protected $response;

    /** @var string */
    protected $xml;

    /** @var DOMDocument */
    protected $document;

    /**
     * @param Response $response
     *
     * @throws Result\Exception
     */
    public function __construct(Response $response)
    {
        $this->response = $response;

        $this->xml = $response->getBody();

        if ( ! ($this->document = Security::scan($this->xml, new DOMDocument()))) {
            throw new Result\Exception('Could not parse xml response');
        }
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return string
     */
    public function getXml()
    {
        return $this->xml;
    }

    /**
     * @return DOMDocument
     */
    public function getDocument()
    {
        return $this->document;
    }
}

