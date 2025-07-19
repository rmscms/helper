<?php

namespace RMS\Helper\WebServices;

use SoapClient;
use SoapFault;

abstract class Soap extends WebService
{
    protected SoapClient $client;
    protected int $cache = WSDL_CACHE_NONE;
    protected int $version = SOAP_1_2;

    public function __construct()
    {
        $this->client = null;
    }

    abstract protected function url(): string;

    public function withCache(): self
    {
        $this->cache = WSDL_CACHE_BOTH;
        return $this;
    }

    public function withOldSoap(): self
    {
        $this->version = SOAP_1_1;
        return $this;
    }

    protected function client(): SoapClient
    {
        if (!$this->client) {
            try {
                $this->client = new SoapClient($this->url(), [
                    'soap_version' => $this->version,
                    'cache_wsdl' => $this->cache,
                    'encoding' => 'UTF-8',
                    'connection_timeout' => $this->timeout,
                    'stream_context' => $this->getStreamHttp(),
                ]);
            } catch (SoapFault $e) {
                $this->lastException = $e;
                throw $e;
            }
        }
        return $this->client;
    }

    protected function getStreamHttp()
    {
        return stream_context_create([
            'http' => [
                'protocol_version' => '1.0',
            ],
        ]);
    }

    public function call(string $method, array $parameters = []): mixed
    {
        try {
            $this->withParameters($parameters);
            return $this->client()->__soapCall($method, $this->parameters);
        } catch (SoapFault $e) {
            $this->lastException = $e;
            return false;
        }
    }
}
