<?php

namespace Attraqt\Fredhopper\Api;

use Laminas\Http\Request;
use RuntimeException;

/**
 * Service api client for Fredhopper service module
 *
 * @author      Stefan Jaroschek
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2020 Tofex UG (http://www.tofex.de)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Service
    extends Api
{
    /**
     * @param string $triggerId
     * @param string $name
     *
     * @return Service\Result\Status
     * @throws Service\Exception
     * @throws Service\Result\Status\Exception
     */
    public function status($triggerId, $name = 'load-data')
    {
        $uri = $this->getBaseUri() . '/trigger/' . $name . '/' . $triggerId . '/status';

        $client = static::getHttpClient();

        $client->resetParameters(true);
        $client->setUri($uri);

        $response = $client->send();

        return new Service\Result\Status($response);
    }

    /**
     * @param string $dataId
     * @param string $name
     * @param string $triggerId
     *
     * @return Service\Result\Trigger
     * @throws Service\Exception
     * @throws Service\Result\Trigger\Exception
     */
    public function trigger($dataId, $name = 'load-data', $triggerId = null)
    {
        $uri = $this->getBaseUri() . '/trigger/' . $name . ($triggerId ? '/' . $triggerId : '');

        $client = static::getHttpClient();

        $client->setMethod(Request::METHOD_PUT);
        $client->resetParameters(true);
        $client->setUri($uri);
        $client->setRawBody('data-id=' . $dataId);
        $this->addHeader('Content-Type', 'text/plain');

        $response = $client->send();

        return new Service\Result\Trigger($response, $triggerId);
    }

    /**
     * @param string  $filePath
     * @param boolean $incremental
     * @param string  $dataId
     *
     * @return Service\Result\Upload
     * @throws Service\Exception
     * @throws Service\Result\Upload\Exception
     */
    public function upload($filePath, $incremental = false, $dataId = null)
    {
        $uri = $this->getBaseUri() . '/data/input/';

        $datafile = 'data' . ($incremental ? '-incremental' : '') . '.zip';

        if ($dataId) {
            $uri .= $dataId . '/' . $datafile;
        } else {
            $checksum = @md5_file($filePath);

            if ($checksum === false) {
                $message = sprintf('Can get MD5 from file %s.', $filePath);
                throw new Service\Exception($message);
            }

            $uri .= $datafile . '?checksum=' . $checksum;
        }

        $fileResource = @fopen($filePath, 'r');

        if ($fileResource === false) {
            $message = sprintf('Can not open file %s for reading.', $filePath);
            throw new Service\Exception($message);
        }

        $client = static::getHttpClient();

        $client->setMethod(Request::METHOD_PUT);
        $client->resetParameters(true);
        $client->setUri($uri);
        $client->setRawBody($fileResource);
        $this->addHeader('Content-Type', 'application/zip');

        try {
            $response = $client->send();
        } catch (RuntimeException $exception) {
            fclose($fileResource);

            throw $exception;
        }

        fclose($fileResource);

        return new Service\Result\Upload($response, $dataId);
    }

    /**
     * @return string
     */
    protected function getBaseUri()
    {
        return sprintf('%s://my.%s.fredhopperservices.com/%s:%s', $this->secure ? 'https' : 'http', $this->region,
            $this->service, $this->instance);
    }
}

