<?php

use App\Support\Money;

if (! function_exists('rupiah')) {
    function rupiah(int|float|null $amount): string
    {
        return Money::rupiah($amount);
    }
}
