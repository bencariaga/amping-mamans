<?php

namespace App\Actions\TariffList;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class StoreTariffList
{
    protected $createTariffList;
    protected $updateAllTariffStatuses;

    public function __construct(
        CreateTariffList $createTariffList,
        UpdateAllTariffStatuses $updateAllTariffStatuses
    ) {
        $this->createTariffList = $createTariffList;
        $this->updateAllTariffStatuses = $updateAllTariffStatuses;
    }

    public function execute(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'effectivity_date' => ['required', 'date_format:Y-m-d', 'after:today', Rule::unique('tariff_lists', 'effectivity_date')],
                'selectedServices' => ['required', 'array', 'min:1'],
                'selectedServices.*' => ['required', 'exists:services,service_id'],
            ], [
                'selectedServices.min' => 'Please check at least one service type to include in this draft.',
                'effectivity_date.unique' => 'The selected effectivity date is already taken by another tariff list version.',
            ]);

            $this->createTariffList->execute($request->effectivity_date, $request->selectedServices);
            $this->updateAllTariffStatuses->execute();

            return response()->json(['message' => 'Tariff list version has been added.'], 200);
        } catch (Exception $e) {
            if ($e->getMessage() === 'You cannot create more than 9 tariff list versions in the same month') {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            Log::error('Failed to create tariff list', ['error' => $e->getMessage()]);

            return response()->json(['message' => 'Failed to create tariff list. Please try again.'], 500);
        }
    }
}
