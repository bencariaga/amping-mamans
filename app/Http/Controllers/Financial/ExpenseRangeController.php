<?php

namespace App\Http\Controllers\Financial;

use App\Actions\ExpenseRange\UpdateExpenseRanges;
use App\Actions\ExpenseRange\ValidateAndFormatExpenseRanges;
use App\Http\Controllers\Controller;
use App\Support\Number;
use Illuminate\Support\Str;

class ExpenseRangeController extends Controller
{
    public function formatNumericValue(string $value): string
    {
        $intValue = (int) $value;

        return Number::format($intValue, 0);
    }

    public function stripZerofill(string $value): string
    {
        return (string) Number::intval($value);
    }

    protected function stripCommas(?string $value): string
    {
        if ($value === null || $value === '') {
            return '0';
        }

        return Str::replace(',', '', $value);
    }

    public function validateAndFormatRanges(string $tariffListId, array $ranges, ValidateAndFormatExpenseRanges $validateAndFormat): array
    {
        return $validateAndFormat->execute($tariffListId, $ranges);
    }

    public function updateRangesForTariffList(string $tariffListId, array $ranges, UpdateExpenseRanges $updateRanges): void
    {
        $updateRanges->execute($tariffListId, $ranges);
    }
}
