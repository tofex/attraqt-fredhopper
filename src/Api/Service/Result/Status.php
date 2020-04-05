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
class Status
    extends Result
{
    /** Define possible states */
    const UNKNOWN = 'UNKNOWN';
    const SCHEDULED = 'SCHEDULED';
    const RUNNING = 'RUNNING';
    const DELAYED = 'DELAYED';
    const SUCCESS = 'SUCCESS';
    const FAILURE = 'FAILURE';

    /** @var int */
    protected $code = 200;

    /** @var array */
    protected $states;

    /** @var string */
    protected $status;

    /** @var string */
    protected $message;

    /**
     * @param Response $response
     *
     * @throws Status\Exception
     * @throws \Attraqt\Fredhopper\Api\Service\Exception
     */
    public function __construct(Response $response)
    {
        $this->states = array(
            static::UNKNOWN   => 'No known state yet: trigger has not yet been picked up',
            static::SCHEDULED => 'Trigger has been picked up, and will start execution soon',
            static::RUNNING   => 'Triggered job is running currently',
            static::DELAYED   => 'Triggered job is ready to run, but delayed (for example due to insufficient capacity)',
            static::SUCCESS   => 'Triggered job has finished successfully',
            static::FAILURE   => 'Triggered job has failed',
        );

        parent::__construct($response);

        $statusRegex = '/(' . implode('|', array_keys($this->states)) . ')/';

        $matches = array();

        if ( ! preg_match($statusRegex, $response->getBody(), $matches)) {
            throw new Status\Exception('Could not find status code');
        }

        $this->status = $matches[ 0 ];

        $this->message = trim(str_replace($matches[ 0 ], '', $response->getBody()));
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getStatusText()
    {
        if (isset($this->states[ $this->status ])) {
            return $this->states[ $this->status ];
        }
        return '';
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return bool
     */
    public function isWaiting()
    {
        return in_array($this->status, array(static::DELAYED, static::SCHEDULED, static::UNKNOWN));
    }

    /**
     * @return bool
     */
    public function isRunning()
    {
        return $this->status == static::RUNNING;
    }

    /**
     * @return bool
     */
    public function isFinished()
    {
        return in_array($this->status, array(static::FAILURE, static::SUCCESS));
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->status == static::SUCCESS;
    }
}

