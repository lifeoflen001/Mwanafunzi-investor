<?php

namespace App\Services;

use App\Support\Money;

class MoneyFormatter
{
    public function format(string|int|float|null $amount, string $currency = 'TZS'): string
    {
        return Money::fromDecimal($amount, $currency)->format();
    }
}
