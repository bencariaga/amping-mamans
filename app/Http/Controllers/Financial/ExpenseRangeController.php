<?php

namespace App\Http\Controllers\Financial;

use App\Actions\ExpenseRange\CheckOverlap;
use App\Actions\ExpenseRange\FormatNumericValue;
use App\Actions\ExpenseRange\StripZerofill;
use App\Actions\ExpenseRange\UpdateRangesForTariffList;
use App\Actions\ExpenseRange\ValidateAndFormatExpenseRanges;
use App\Http\Controllers\Controller;

class ExpenseRangeController extends Controller
{
    public function formatNumericValue(string $value): string
    {
        return app(FormatNumericValue::class)->execute($value);
    }

    public function stripZerofill(string $value): string
    {
        return app(StripZerofill::class)->execute($value);
    }

    public function checkOverlap(array $ranges): bool
    {
        return app(CheckOverlap::class)->execute($ranges);
    }

    public function validateAndFormatRanges(string $tariffListId, array $ranges): array
    {
        return app(ValidateAndFormatExpenseRanges::class)->execute($tariffListId, $ranges);
    }

    public function updateRangesForTariffList(string $tariffListId, array $ranges): void
    {
        app(UpdateRangesForTariffList::class)->execute($tariffListId, $ranges);
    }
}
