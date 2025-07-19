<?php

if (! function_exists('displayAmount')) {
    function displayAmount($amount, $sign = null): string
    {
        if ($sign === null) {
            $sign = config('helpers.currency');
        }
        return number_format($amount, 0, '.', ',') . ' ' . (string)$sign;
    }
}

if (! function_exists('changeNumberToEn')) {
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
        return str_replace(array_values($per), array_keys($per), $string);
    }
}
