<?php

if (! function_exists('displayAmount')) {
    function displayAmount($amount, $sign = null): string
    {
        if ($sign === null)
            $sign = config('helpers.currency');
        return number_format($amount, 0, '.', ',') . ' ' . (string)$sign;
    }
}
