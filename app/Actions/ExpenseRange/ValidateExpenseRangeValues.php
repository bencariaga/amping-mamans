<?php

namespace App\Actions\ExpenseRange;

use App\Exceptions\FinanceException;
use App\ValueObjects\Money;

class ValidateExpenseRangeValues
{
    public function execute(float $min, float $max, float $coverage): void
    {
        $minMoney = new Money($min);
        $maxMoney = new Money($max);

        if ($minMoney->isGreaterThan($maxMoney) || $minMoney->equals($maxMoney)) {
            throw FinanceException::invalidExpenseRange($min, $max);
        }

        if ($coverage < 0 || $coverage > 100) {
            throw FinanceException::invalidCoveragePercent($coverage);
        }
    }
}
