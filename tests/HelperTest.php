<?php

namespace RMS\Helper\Tests;

use Orchestra\Testbench\TestCase;
use RMS\Helper\HelperServiceProvider;

class HelperTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [HelperServiceProvider::class];
    }

    public function testDisplayAmountWithoutSign()
    {
        $this->app['config']->set('helpers.currency', 'تومان');
        $result = displayAmount(1000);
        $this->assertEquals('1,000 تومان', $result);
    }

    public function testDisplayAmountWithCustomSign()
    {
        $result = displayAmount(1000, 'ریال');
        $this->assertEquals('1,000 ریال', $result);
    }

    public function testDisplayAmountWithZero()
    {
        $this->app['config']->set('helpers.currency', 'تومان');
        $result = displayAmount(0);
        $this->assertEquals('0 تومان', $result);
    }

    public function testChangeNumberToEnWithPersianNumbers()
    {
        $result = changeNumberToEn('۱۲۳۴۵۶');
        $this->assertEquals('123456', $result);
    }

    public function testChangeNumberToEnWithArabicNumbers()
    {
        $result = changeNumberToEn('١٢٣٤٥٦');
        $this->assertEquals('123456', $result);
    }

    public function testChangeNumberToEnWithEnglishNumbers()
    {
        $result = changeNumberToEn('123456');
        $this->assertEquals('123456', $result);
    }

    public function testChangeNumberToEnWithEmptyString()
    {
        $result = changeNumberToEn('');
        $this->assertEquals('', $result);
    }
}
