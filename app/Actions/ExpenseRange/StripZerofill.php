<?php

namespace App\Actions\ExpenseRange;

use App\Support\Number;

class StripZerofill
{
    public function execute(string $value): string
    {
        return (string) Number::intval($value);
    }
}
