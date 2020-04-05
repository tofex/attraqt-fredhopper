<?php

namespace Attraqt\Fredhopper\Api\Service;

use Laminas\Http\Response;

/**
 * Service api client response for Fredhopper service module
 *
 * @author      Stefan Jaroschek
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2020 Tofex UG (http://www.tofex.de)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
abstract class Result
{
    /** Define regular expression to match data-ids */
    const DATA_ID_REGEX = '/[0-9]{4}-[0-9]{2}-[0-9]{2}_[0-9]{2}-[0-9]{2}-[0-9]{2}/';

    /** @var int */
    protected $code = 201;

    /** @var Response */
    protected $response;

    /**
     * @param Response $response
     *
     * @throws Exception
     */
    public function __construct(Response $response)
    {
        $this->response = $response;

        if ($response->getStatusCode() !== $this->code) {
            $message = sprintf('Unexpected HTTP status code returned %s', $response->getStatusCode());

            throw new Exception($message);
        }
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
