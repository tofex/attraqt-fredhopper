<?php

namespace Attraqt\Fredhopper\Api\Suggest;

use Laminas\Http\Response;
use Laminas\Json\Json;

/**
 * Suggest api response for Fredhopper service module
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
    protected $json;

    /** @var array */
    protected $data;

    /**
     * @param Response $response
     *
     * @throws Result\Exception
     */
    public function __construct(Response $response)
    {
        $this->response = $response;

        if ($response->getStatusCode() !== 200) {
            $message = sprintf('Unexpected HTTP status code returned %s', $response->getStatusCode());

            throw new Result\Exception($message);
        }

        $this->json = $response->getBody();

        $this->data = Json::decode($this->json);
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
    public function getJson()
    {
        return $this->json;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}

