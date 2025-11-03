<?php

namespace App\Actions\ExpenseRange;

use App\Support\Number;

class FormatNumericValue
{
    public function execute(string $value): string
    {
        $intValue = (int) $value;
        return Number::format($intValue, 0);
    }
}
