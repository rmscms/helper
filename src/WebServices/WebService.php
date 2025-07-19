<?php

namespace RMS\Helper\WebServices;

abstract class WebService
{
    protected int $timeout = 60;
    protected ?object $lastException = null;
    protected array $parameters = [];

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
     * Get the last exception thrown during the request
     *
     * @return object|null
     */
    public function getLastException(): ?object
    {
        return $this->lastException;
    }
}
