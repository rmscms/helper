<?php

namespace RMS\Helper\WebServices;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

abstract class Rest extends WebService
{
    protected bool $verify = true;
    protected bool $httpErrors = false;
    protected bool $debug = false;
    protected ?array $basicAuth = null;
    protected bool $jsonResponse = true;
    protected array $headers = [
        'User-Agent' => 'RMS-Helper/1.0',
        'Accept' => 'application/json',
        'Cache-Control' => 'no-cache',
    ];
    protected array $curlOptions = [
        CURLOPT_FOLLOWLOCATION => true,
    ];

    public function __construct()
    {
        $this->client = new Client();
    }

    abstract protected function url(string $uri = ''): string;

    abstract protected function requestMethod(): string;

    public function unVerify(): self
    {
        $this->verify = false;
        return $this;
    }

    public function withHttpErrors(): self
    {
        $this->httpErrors = true;
        return $this;
    }

    public function withDebug(): self
    {
        $this->debug = true;
        return $this;
    }

    public function withTimeout(int $timeout): self
    {
        $this->timeout = $timeout;
        return $this;
    }

    public function withCurlOptions(array $options): self
    {
        $this->curlOptions = array_merge($this->curlOptions, $options);
        return $this;
    }

    public function withUserAgent(string $userAgent): self
    {
        $this->headers['User-Agent'] = $userAgent;
        return $this;
    }

    public function withHeaders(array $headers): self
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    public function withBasicAuth(string $username, string $password): self
    {
        $this->basicAuth = [$username, $password];
        return $this;
    }

    public function noJson(): self
    {
        $this->jsonResponse = false;
        return $this;
    }

    protected function client(): Client
    {
        if (!$this->client) {
            $this->client = new Client();
        }
        return $this->client;
    }

    public function send(string $uri = ''): mixed
    {
        $requestOptions = [
            'headers' => $this->headers,
            'verify' => $this->verify,
            'http_errors' => $this->httpErrors,
            'debug' => $this->debug,
            'curl' => $this->curlOptions,
        ];

        if (!empty($this->parameters)) {
            $requestOptions['json'] = $this->parameters;
        }

        if ($this->basicAuth) {
            $requestOptions['auth'] = $this->basicAuth;
        }

        if (isset($this->timeout)) {
            $requestOptions['timeout'] = $this->timeout;
        }

        try {
            $response = $this->client()->request($this->requestMethod(), $this->url($uri), $requestOptions);
            $content = $response->getBody()->getContents();
            return $this->jsonResponse ? json_decode($content, true) : $content;
        } catch (GuzzleException $e) {
            $this->lastException = $e;
            return false;
        }
    }
}
