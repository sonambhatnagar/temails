<?php

namespace App\Events;


/**
 * Class EmailSentEvent
 * @package App\Events
 */
class EmailSentEvent extends Event
{

    /**
     * @var array
     */
    private $requestData;
    /**
     * @var array
     */
    private $responseData;
    /**
     * @var string
     */
    private $status;

    /**
     * Create a new event instance.
     *
     * @param array  $requestData
     * @param array  $responseData
     * @param string $status
     */
    public function __construct(array $requestData, array $responseData, string $status)
    {
        $this->requestData  = $requestData;
        $this->responseData = $responseData;
        $this->status       = $status;
    }


    /**
     * @return array
     */
    public function getRequestData(): array
    {
        return $this->requestData;
    }

    /**
     * @return array
     */
    public function getResponseData(): array
    {
        return $this->responseData;
    }


    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }
}
