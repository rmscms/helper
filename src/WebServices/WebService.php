<?php

namespace RMS\Helper\WebServices;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

abstract class WebService
{
    protected int $timeout = 60;
    protected ?GuzzleException $lastException = null;
    protected array $parameters = [];
    protected ?Client $client = null;

    /**
     * Add or merge parameters for the request
     *
     * @param array $params
     * @return $this
     */
    public function withParameters(array $params): self
    {
        $this->parameters = array_merge($this->parameters, $params);
        return $this;
    }

    /**
     * Get the HTTP client instance
     *
     * @return Client
     */
    abstract protected function client(): Client;

    /**
     * Get the last exception thrown during the request
     *
     * @return GuzzleException|null
     */
    public function getLastException(): ?GuzzleException
    {
        return $this->lastException;
    }
}
