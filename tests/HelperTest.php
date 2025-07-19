<?php

namespace RMS\Helper\Tests;

use Orchestra\Testbench\TestCase;
use RMS\Helper\HelperServiceProvider;
use Carbon\Carbon;

class HelperTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [HelperServiceProvider::class];
    }

    public function testDisplayAmountWithoutSign()
    {
        $this->app['config']->set('helpers.currency', 'تومان');
        $result = \RMS\Helper\displayAmount(1000);
        $this->assertEquals('1,000 تومان', $result);
    }

    public function testDisplayAmountWithCustomSign()
    {
        $result = \RMS\Helper\displayAmount(1000, 'ریال');
        $this->assertEquals('1,000 ریال', $result);
    }

    public function testDisplayAmountWithZero()
    {
        $this->app['config']->set('helpers.currency', 'تومان');
        $result = \RMS\Helper\displayAmount(0);
        $this->assertEquals('0 تومان', $result);
    }

    public function testChangeNumberToEnWithPersianNumbers()
    {
        $result = \RMS\Helper\changeNumberToEn('۱۲۳۴۵۶');
        $this->assertEquals('123456', $result);
    }

    public function testChangeNumberToEnWithArabicNumbers()
    {
        $result = \RMS\Helper\changeNumberToEn('١٢٣٤٥٦');
        $this->assertEquals('123456', $result);
    }

    public function testChangeNumberToEnWithEnglishNumbers()
    {
        $result = \RMS\Helper\changeNumberToEn('123456');
        $this->assertEquals('123456', $result);
    }

    public function testChangeNumberToEnWithEmptyString()
    {
        $result = \RMS\Helper\changeNumberToEn('');
        $this->assertEquals('', $result);
    }

    public function testPersianDate()
    {
        $date = Carbon::create(2025, 7, 20, 14, 0, 0);
        $result = \RMS\Helper\persian_date($date);
        $this->assertStringContainsString('1404/04/29', $result);
    }

    public function testPersianDateInvalid()
    {
        $this->expectException(\InvalidArgumentException::class);
        \RMS\Helper\persian_date('invalid-date');
    }

    public function testGregorianDate()
    {
        $result = \RMS\Helper\gregorian_date('1404/04/29');
        $this->assertEquals('2025/07/20', $result);
    }

    public function testGregorianDateWithTime()
    {
        $result = \RMS\Helper\gregorian_date('1404/04/29 14:00:00');
        $this->assertEquals('2025/07/20 14:00:00', $result);
    }

    public function testGregorianDateInvalid()
    {
        $this->expectException(\InvalidArgumentException::class);
        \RMS\Helper\gregorian_date('invalid/date/format');
    }

    public function testPersianToTimestamp()
    {
        $timestamp = \RMS\Helper\persian_to_timestamp('1404/04/29');
        $expected = Carbon::create(2025, 7, 20)->startOfDay()->getTimestamp();
        $this->assertEquals($expected, $timestamp);
    }

    public function testIsValidPersianDate()
    {
        $this->assertTrue(\RMS\Helper\is_valid_persian_date('1404/04/29'));
        $this->assertFalse(\RMS\Helper\is_valid_persian_date('1404/13/29'));
    }

    public function testPersianDateDiff()
    {
        $diff = \RMS\Helper\persian_date_diff('1404/04/29', '1404/04/30');
        $this->assertEquals(1, $diff);
    }

    public function testPersianNow()
    {
        $result = \RMS\Helper\persian_now('Y/m/d');
        $this->assertStringContainsString(Carbon::now()->format('1404'), $result);
    }
}
