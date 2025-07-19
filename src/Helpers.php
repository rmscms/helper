<?php

namespace RMS\Helper;

use Carbon\Carbon;
use Morilog\Jalali\CalendarUtils;
use InvalidArgumentException;

if (! function_exists('RMS\Helper\displayAmount')) {
    function displayAmount($amount, $sign = null): string
    {
        if ($sign === null) {
            $sign = config('helpers.currency', 'تومان');
        }
        return number_format($amount, 0, '.', ',') . ' ' . (string)$sign;
    }
}

if (! function_exists('RMS\Helper\changeNumberToEn')) {
    function changeNumberToEn(string $string): string
    {
        $arabic = [
            '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵',
            '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹', '0' => '۰'
        ];
        $per = [
            '0' => '٠', '1' => '١', '2' => '٢', '3' => '٣', '4' => '٤',
            '5' => '٥', '6' => '٦', '7' => '٧', '8' => '٨', '9' => '٩'
        ];
        $string = str_replace(array_values($arabic), array_keys($arabic), $string);
        $string = str_replace(array_values($per), array_keys($per), $string);

        return $string;
    }
}

if (! function_exists('RMS\Helper\persian_date')) {
    function persian_date($date, string $format = 'Y/m/d H:i:s'): string
    {
        $timestamp = $date instanceof Carbon ? $date->getTimestamp() : strtotime($date);
        if ($timestamp === false) {
            throw new InvalidArgumentException('Invalid date format');
        }
        return CalendarUtils::strftime($format, $timestamp);
    }
}

if (! function_exists('RMS\Helper\gregorian_date')) {
    function gregorian_date($date, string $separator = '/'): string
    {
        $dateString = $date instanceof Carbon ? $date->format('Y' . $separator . 'm' . $separator . 'd H:i:s') : $date;
        $times = explode(' ', $dateString);
        $dates = explode($separator, $times[0]);

        if (count($dates) !== 3) {
            throw new InvalidArgumentException('Invalid Persian date format. Expected: Y' . $separator . 'm' . $separator . 'd');
        }

        // تبدیل به int برای checkdate
        $year = (int)$dates[0];
        $month = (int)$dates[1];
        $day = (int)$dates[2];

        if (!checkdate($month, $day, $year)) {
            throw new InvalidArgumentException('Invalid Persian date values');
        }

        $newDate = CalendarUtils::toGregorian($year, $month, $day);
        if (!$newDate || !checkdate($newDate[1], $newDate[2], $newDate[0])) {
            throw new InvalidArgumentException('Failed to convert Persian date to Gregorian');
        }

        $result = Carbon::createFromDate($newDate[0], $newDate[1], $newDate[2])->format('Y' . $separator . 'm' . $separator . 'd');
        return count($times) > 1 ? $result . ' ' . $times[1] : $result;
    }
}
