<?php

namespace RMS\Helper\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Orchestra\Testbench\TestCase;
use RMS\Helper\WebServices\Rest;

class WebServiceTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [\RMS\Helper\HelperServiceProvider::class];
    }

    public function testSendJsonResponseWithParameters()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(['success' => true])),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = $this->getMockBuilder(Rest::class)->onlyMethods(['url', 'requestMethod', 'client'])->getMockForAbstractClass();

        $service->method('url')->willReturn('https://api.example.com/test');
        $service->method('requestMethod')->willReturn('POST');
        $service->method('client')->willReturn($client);

        $result = $service->withParameters(['key' => 'value'])->send();
        $this->assertEquals(['success' => true], $result);
    }

    public function testSendWithException()
    {
        $mock = new MockHandler([
            new \GuzzleHttp\Exception\ConnectException('Connection failed', new \GuzzleHttp\Psr7\Request('GET', 'test')),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = $this->getMockBuilder(Rest::class)->onlyMethods(['url', 'requestMethod', 'client'])->getMockForAbstractClass();

        $service->method('url')->willReturn('https://api.example.com/test');
        $service->method('requestMethod')->willReturn('GET');
        $service->method('client')->willReturn($client);

        $result = $service->withParameters(['key' => 'value'])->send();
        $this->assertFalse($result);
        $this->assertInstanceOf(\GuzzleHttp\Exception\ConnectException::class, $service->getLastException());
    }
}
