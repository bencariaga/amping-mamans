<?php

namespace App\Actions\TariffList;

use App\Http\Controllers\Financial\ExpenseRangeController;
use App\Models\Operation\TariffList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UpdateTariffListRanges
{
    protected $expenseRangeController;
    protected $collectAllRanges;

    public function __construct(
        ExpenseRangeController $expenseRangeController,
        CollectAllRanges $collectAllRanges
    ) {
        $this->expenseRangeController = $expenseRangeController;
        $this->collectAllRanges = $collectAllRanges;
    }

    public function execute(Request $request, string $tariffListId): void
    {
        Log::info('Update request data:', $request->all());

        $tariffList = TariffList::where('tariff_list_id', $tariffListId)->firstOrFail();

        $ranges = $this->collectAllRanges->execute($request);

        if ($this->expenseRangeController->checkOverlap($ranges)) {
            throw ValidationException::withMessages([
                'general' => ['One or more expense ranges overlap. Please correct them before saving.'],
            ]);
        }

        DB::beginTransaction();

        try {
            $validatedAndFormattedRanges = $this->expenseRangeController->validateAndFormatRanges(
                $tariffList->tariff_list_id,
                $ranges
            );

            $this->expenseRangeController->updateRangesForTariffList(
                $tariffList->tariff_list_id,
                $validatedAndFormattedRanges
            );

            $tariffList->save();

            DB::commit();

            Log::info('Successfully updated tariff list with ranges', [
                'tariff_list_id' => $tariffList->tariff_list_id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
