<?php

namespace RMS\Helper\Tests;

use Orchestra\Testbench\TestCase;
use RMS\Helper\WebServices\Soap;
use SoapClient;
use SoapFault;
use ReflectionProperty;

class SoapServiceTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [\RMS\Helper\HelperServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();
        if (!extension_loaded('soap')) {
            $this->markTestSkipped('SOAP extension is not enabled.');
        }
    }

    public function testSoapCallSuccess()
    {
        $mock = $this->createMock(SoapClient::class);
        $mock->expects($this->once())
            ->method('__soapCall')
            ->with('testMethod', ['key' => 'value'])
            ->willReturn(['success' => true]);

        $service = $this->getMockBuilder(Soap::class)
            ->onlyMethods(['url'])
            ->getMockForAbstractClass();
        $service->method('url')->willReturn('https://api.example.com?wsdl');

        // تنظیم client با Reflection
        $reflection = new ReflectionProperty(Soap::class, 'client');
        $reflection->setAccessible(true);
        $reflection->setValue($service, $mock);

        $result = $service->withParameters(['key' => 'value'])->call('testMethod');
        $this->assertEquals(['success' => true], $result);
    }

    public function testSoapCallWithException()
    {
        $mock = $this->createMock(SoapClient::class);
        $mock->expects($this->once())
            ->method('__soapCall')
            ->willThrowException(new SoapFault('Server', 'SOAP error'));

        $service = $this->getMockBuilder(Soap::class)
            ->onlyMethods(['url'])
            ->getMockForAbstractClass();
        $service->method('url')->willReturn('https://api.example.com?wsdl');

        // تنظیم client با Reflection
        $reflection = new ReflectionProperty(Soap::class, 'client');
        $reflection->setAccessible(true);
        $reflection->setValue($service, $mock);

        $result = $service->withParameters(['key' => 'value'])->call('testMethod');
        $this->assertFalse($result);
        $this->assertInstanceOf(SoapFault::class, $service->getLastException());
    }
}
